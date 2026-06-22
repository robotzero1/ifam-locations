<?php

class Ifam_Locations_Endpoints
{

    /**
     * Register REST API routes
     */
    public static function register_rest_routes()
    {
        // Remote API access for the locations
        register_rest_route('locations/v1', '/location_data', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_location_data'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('locations/v1', '/reorder', [
            'methods' => 'POST',
            'callback' => [self::class, 'reorder_locations'],
            'permission_callback' => [self::class, 'edit_permission_check'],
            'args' => [
                'order' => [
                    'required' => true,
                    'type' => 'array',
                    'items' => ['type' => 'integer'],
                    'validate_callback' => function ($value) {
                        return is_array($value) && !empty($value);
                    },
                ],
            ],
        ]);
    }

    // combines previous, current and next location data
    public static function get_location_data()
    {
        $current = self::get_current_location();

        $data = [
            'previous' => self::get_previous_location($current),
            'current'  => $current,
            'next'     => self::get_next_location($current),
        ];

        return rest_ensure_response($data);
    }

    public static function edit_permission_check()
    {
        return current_user_can('manage_options');
    }

    public static function reorder_locations(WP_REST_Request $request)
    {
        global $wpdb;
        $table = self::table();

        $order = array_map('intval', $request->get_param('order'));

        foreach ($order as $position => $id) {
            $wpdb->update(
                $table,
                ['visit_order' => $position + 1],
                ['id' => $id],
                ['%d'],
                ['%d']
            );
        }

        return rest_ensure_response(['success' => true]);
    }


    public static function table()
    {
        global $wpdb;
        return $wpdb->prefix . 'ifam_locations';
    }


    // Current location = the visited location with the highest visit_order
    public static function get_current_location()
    {
        global $wpdb;
        $table = self::table();

        return $wpdb->get_row(
            "SELECT * FROM `{$table}`
             WHERE visited = 1
             ORDER BY visit_order DESC
             LIMIT 1",
            ARRAY_A
        );
    }

    // Previous location = the lowest visit_order below the current one
    public static function get_previous_location($current = null)
    {
        global $wpdb;
        $table = self::table();

        $current = $current ?? self::get_current_location();
        $current_order = $current ? (int) $current['visit_order'] : 0;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM `{$table}`
             WHERE visit_order < %d
             ORDER BY visit_order DESC
             LIMIT 1",
                $current_order
            ),
            ARRAY_A
        );
    }

    // Next location = the lowest visit_order above the current one
    public static function get_next_location($current = null)
    {
        global $wpdb;
        $table = self::table();

        $current = $current ?? self::get_current_location();
        $current_order = $current ? (int) $current['visit_order'] : 0;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM `{$table}`
             WHERE visit_order > %d
             ORDER BY visit_order ASC
             LIMIT 1",
                $current_order
            ),
            ARRAY_A
        );
    }
}

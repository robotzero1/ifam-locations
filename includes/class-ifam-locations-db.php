<?php

class Ifam_Locations_DB
{

    public static function table()
    {
        global $wpdb;
        return $wpdb->prefix . 'ifam_locations';
    }

    public static function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table = self::table();

        $sql = "CREATE TABLE `{$table}` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `location_name` VARCHAR(255) NOT NULL,
            `location_type` VARCHAR(100),
            `region_type` VARCHAR(100),
            `region_name` VARCHAR(255),
            `orbiting_type` VARCHAR(100),
            `orbiting_name` VARCHAR(255),
            `surface_conditions` TEXT,
            `weather` VARCHAR(255),
            `description` TEXT,
            `local_species_name` VARCHAR(255),
            `local_species_description` TEXT,
            `visited` BOOLEAN DEFAULT 0,
            `visited_times` INT DEFAULT 0,
            `visit_order` INT DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate";

        // Include the WordPress dbDelta function
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);
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

    // Next location = the lowest visit_order above the current one
    public static function get_next_location()
    {
        global $wpdb;
        $table = self::table();

        $current = self::get_current_location();
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

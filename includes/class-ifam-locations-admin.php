<?php

class Ifam_Locations_Admin
{

    public static function menu()
    {
        add_menu_page(
            __('iFam Locations', 'ifam-locations'),
            __('iFam Locations', 'ifam-locations'),
            'manage_options',
            'ifam-locations',
            [self::class, 'page'],
            'dashicons-location-alt'
        );
    }

    public static function page()
    {
        global $wpdb;
        $table = Ifam_Locations_DB::table();

        // Handle delete
        if (isset($_POST['delete_location'])) {
            check_admin_referer('ifam_locations_delete_' . (int) $_POST['location_id']);
            $wpdb->delete($table, ['id' => (int) $_POST['location_id']], ['%d']);
        }

        // Handle save (create/update)
        if (isset($_POST['save_location'])) {
            check_admin_referer('ifam_locations_save', 'ifam_locations_nonce');
            $data = [
                'location_name' => wp_unslash($_POST['location_name']),
                'location_type' => wp_unslash($_POST['location_type']),
                'region_type' => wp_unslash($_POST['region_type']),
                'region_name' => wp_unslash($_POST['region_name']),
                'orbiting_type' => wp_unslash($_POST['orbiting_type']),
                'orbiting_name' => wp_unslash($_POST['orbiting_name']),
                'surface_conditions' => wp_unslash($_POST['surface_conditions']),
                'weather' => wp_unslash($_POST['weather']),
                'description' => wp_unslash($_POST['description']),
                'local_species_name' => wp_unslash($_POST['local_species_name']),
                'local_species_description' => wp_unslash($_POST['local_species_description']),
                'visited' => isset($_POST['visited']) ? 1 : 0,
                'visited_times' => (int) $_POST['visited_times'],
                'visit_order' => (int) $_POST['visit_order'],
                'updated_at' => current_time('mysql')
            ];

            if (!empty($_POST['location_id'])) {
                // Update
                $wpdb->update($table, $data, ['id' => (int) $_POST['location_id']]);
            } else {
                // Create
                $wpdb->insert($table, $data);
            }
        }

        // Get location to edit
        $editing = null;
        if (isset($_GET['edit'])) {
            $editing = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM `{$table}` WHERE id = %d", (int) $_GET['edit']),
                ARRAY_A
            );
        }

        // Get all locations
        $locations = $wpdb->get_results(
            "SELECT * FROM `{$table}` ORDER BY visit_order ASC, location_name ASC",
            ARRAY_A
        );
?>

        <div class="wrap">
            <h1><?php esc_html_e('iFam Locations', 'ifam-locations'); ?></h1>

            <p>
                <?php esc_html_e('List of travel locations.', 'ifam-locations'); ?>
            </p>

            <h2><?php echo $editing ? esc_html__('Edit Location', 'ifam-locations') : esc_html__('Add Location', 'ifam-locations'); ?></h2>

            <form method="post">
                <?php wp_nonce_field('ifam_locations_save', 'ifam_locations_nonce'); ?>
                <input type="hidden" name="location_id" value="<?php echo $editing ? (int) $editing['id'] : ''; ?>">

                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e('Location Name', 'ifam-locations'); ?></th>
                        <td>
                            <input type="text" name="location_name" class="regular-text"
                                value="<?php echo esc_attr($editing['location_name'] ?? ''); ?>" required>
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Location Type', 'ifam-locations'); ?></th>
                        <td>
                            <input type="text" name="location_type" class="regular-text"
                                value="<?php echo esc_attr($editing['location_type'] ?? ''); ?>"
                                placeholder="<?php esc_attr_e('e.g. space station, moon, planet', 'ifam-locations'); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Region Type', 'ifam-locations'); ?></th>
                        <td>
                            <input type="text" name="region_type" class="regular-text"
                                value="<?php echo esc_attr($editing['region_type'] ?? ''); ?>"
                                placeholder="<?php esc_attr_e('e.g. galaxy, solar system', 'ifam-locations'); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Region Name', 'ifam-locations'); ?></th>
                        <td>
                            <input type="text" name="region_name" class="regular-text"
                                value="<?php echo esc_attr($editing['region_name'] ?? ''); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Orbiting Type', 'ifam-locations'); ?></th>
                        <td>
                            <input type="text" name="orbiting_type" class="regular-text"
                                value="<?php echo esc_attr($editing['orbiting_type'] ?? ''); ?>"
                                placeholder="<?php esc_attr_e('e.g. planet, sun', 'ifam-locations'); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Orbiting Name', 'ifam-locations'); ?></th>
                        <td>
                            <input type="text" name="orbiting_name" class="regular-text"
                                value="<?php echo esc_attr($editing['orbiting_name'] ?? ''); ?>"
                                placeholder="<?php esc_attr_e('e.g. Sol, Jupiter', 'ifam-locations'); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Weather', 'ifam-locations'); ?></th>
                        <td>
                            <input type="text" name="weather" class="regular-text"
                                value="<?php echo esc_attr($editing['weather'] ?? ''); ?>"
                                placeholder="<?php esc_attr_e('e.g. Methane drizzle, -12°C', 'ifam-locations'); ?>">
                            <p class="description"><?php esc_html_e('Shown in the header banner next to the location name.', 'ifam-locations'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Surface Conditions', 'ifam-locations'); ?></th>
                        <td>
                            <textarea name="surface_conditions" rows="3" class="large-text"><?php echo esc_textarea($editing['surface_conditions'] ?? ''); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Description', 'ifam-locations'); ?></th>
                        <td>
                            <textarea name="description" rows="5" class="large-text"><?php echo esc_textarea($editing['description'] ?? ''); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Local Species Name', 'ifam-locations'); ?></th>
                        <td>
                            <input type="text" name="local_species_name" class="regular-text"
                                value="<?php echo esc_attr($editing['local_species_name'] ?? ''); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Local Species Description', 'ifam-locations'); ?></th>
                        <td>
                            <textarea name="local_species_description" rows="3" class="large-text"><?php echo esc_textarea($editing['local_species_description'] ?? ''); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Visited', 'ifam-locations'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="visited" value="1"
                                    <?php checked($editing['visited'] ?? false, true); ?>>
                                <?php esc_html_e('Mark as visited', 'ifam-locations'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Visited Times', 'ifam-locations'); ?></th>
                        <td>
                            <input type="number" name="visited_times" class="regular-text"
                                value="<?php echo esc_attr($editing['visited_times'] ?? '0'); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><?php esc_html_e('Visit Order', 'ifam-locations'); ?></th>
                        <td>
                            <input type="number" name="visit_order" class="regular-text"
                                value="<?php echo esc_attr($editing['visit_order'] ?? '0'); ?>">
                        </td>
                    </tr>
                </table>

                <p>
                    <button class="button button-primary" name="save_location">
                        <?php echo $editing ? esc_html__('Update Location', 'ifam-locations') : esc_html__('Save Location', 'ifam-locations'); ?>
                    </button>
                    <?php if ($editing): ?>
                        <a href="?page=ifam-locations" class="button"><?php esc_html_e('Cancel', 'ifam-locations'); ?></a>
                    <?php endif; ?>
                </p>
            </form>

            <h2><?php esc_html_e('Existing Locations', 'ifam-locations'); ?></h2>

            <table class="widefat ifam-locations-sortable">
                <thead>
                    <tr>
                        <th class="drag-handle"></th>
                        <th><?php esc_html_e('Name', 'ifam-locations'); ?></th>
                        <th><?php esc_html_e('Type', 'ifam-locations'); ?></th>
                        <th><?php esc_html_e('Region', 'ifam-locations'); ?></th>
                        <th><?php esc_html_e('Weather', 'ifam-locations'); ?></th>
                        <th><?php esc_html_e('Visited', 'ifam-locations'); ?></th>
                        <th><?php esc_html_e('Visited Times', 'ifam-locations'); ?></th>
                        <th><?php esc_html_e('Visit Order', 'ifam-locations'); ?></th>
                        <th><?php esc_html_e('Actions', 'ifam-locations'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $loc): ?>
                        <tr data-id="<?php echo (int) $loc['id']; ?>">
                            <th class="drag-handle">^</th>
                            <td><?php echo esc_html($loc['location_name']); ?></td>
                            <td><?php echo esc_html($loc['location_type']); ?></td>
                            <td><?php echo esc_html($loc['region_name']); ?></td>
                            <td><?php echo esc_html($loc['weather']); ?></td>
                            <td><?php echo $loc['visited'] ? '&#10003;' : ''; ?></td>
                            <td><?php echo esc_html($loc['visited_times']); ?></td>
                            <td><?php echo esc_html($loc['visit_order']); ?></td>
                            <td>
                                <a href="?page=ifam-locations&edit=<?php echo (int) $loc['id']; ?>" class="button button-small"><?php esc_html_e('Edit', 'ifam-locations'); ?></a>
                                <form method="post" style="display:inline;">
                                    <?php wp_nonce_field('ifam_locations_delete_' . (int) $loc['id']); ?>
                                    <input type="hidden" name="location_id" value="<?php echo (int) $loc['id']; ?>">
                                    <button class="button button-small" name="delete_location"
                                        onclick="return confirm('<?php echo esc_js(__('Delete this location?', 'ifam-locations')); ?>')"><?php esc_html_e('Delete', 'ifam-locations'); ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

<?php
    }
}





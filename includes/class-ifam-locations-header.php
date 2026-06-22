<?php

class Ifam_Locations_Header
{


    public static function banner_html()
    {
        $current = Ifam_Locations_DB::get_current_location();
        $next = Ifam_Locations_DB::get_next_location();

        if (!$current && !$next) {
            return '';
        }

        ob_start();
?>
        <div class="ifam-location-banner">
            <?php if ($current): ?>
                <span class="ifam-location-banner__item">
                    <span class="ifam-location-banner__label">Now visiting:</span>
                    <strong><?php echo esc_html($current['location_name']); ?></strong>
                    <?php if (!empty($current['weather'])): ?>
                        <span class="ifam-location-banner__weather">&#9728; <?php echo esc_html($current['weather']); ?></span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>

            <?php if ($current && $next): ?>
                <span class="ifam-location-banner__separator">&#10142;</span>
            <?php endif; ?>

            <?php if ($next): ?>
                <span class="ifam-location-banner__item">
                    <span class="ifam-location-banner__label">Next stop:</span>
                    <strong><?php echo esc_html($next['location_name']); ?></strong>
                    <?php if (!empty($next['weather'])): ?>
                        <span class="ifam-location-banner__weather">&#9728; <?php echo esc_html($next['weather']); ?></span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>
<?php
        return ob_get_clean();
    }
}

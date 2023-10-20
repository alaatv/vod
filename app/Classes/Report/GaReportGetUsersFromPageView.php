<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-11-04
 * Time: 16:06
 */

namespace App\Classes\Report;

use Google_Service_AnalyticsReporting_GetReportsResponse;

class GaReportGetUsersFromPageView extends GaReportGetPathPageViews
{
    protected function setDimension(): void
    {
        // Init the Dimension object.
        $this->dimension->setName('ga:dimension2');
    }

    protected function format(Google_Service_AnalyticsReporting_GetReportsResponse $reports)
    {
        $out = $this->baseFormat($reports);

        return $out;
    }
}

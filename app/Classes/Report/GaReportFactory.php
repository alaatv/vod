<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-11-03
 * Time: 17:53
 */

namespace App\Classes\Report;

use Google_Client;
use Google_Exception;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Dimension;
use Google_Service_AnalyticsReporting_DimensionFilterClause;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_OrderBy;
use Google_Service_AnalyticsReporting_ReportRequest;

class GaReportFactory
{
    /**
     * @var Google_Client
     */
    private $client;

    private $request;

    private $analytics;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('get page view reporting');
        try {

            $this->client->setAuthConfig(config('ga.json'));
        } catch (Google_Exception $e) {
            throw new $e();
        }
        $this->client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

        $this->analytics = new Google_Service_AnalyticsReporting($this->client);

        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $this->request = $request;
    }

    /**
     * @return GaReportGetPathPageViews
     */
    public function createGaReportGetPathPageViews(): GaReportGetPathPageViews
    {
        $this->request->setViewId(config('ga.VIEW_ID'));

        return new GaReportGetPathPageViews($this->analytics, new Google_Service_AnalyticsReporting_GetReportsRequest(),
            $this->request,
            new Google_Service_AnalyticsReporting_DateRange(), [
                new Google_Service_AnalyticsReporting_Metric(),
                new Google_Service_AnalyticsReporting_Metric(),
            ], new Google_Service_AnalyticsReporting_Dimension(), new Google_Service_AnalyticsReporting_OrderBy(),
            new Google_Service_AnalyticsReporting_DimensionFilterClause());
    }

    /**
     * @return GaReportGetUsersFromPageView
     */
    public function createGaReportGetUsersFromPageView(): GaReportGetUsersFromPageView
    {
        $this->request->setViewId(config('ga.USER_ID'));

        return new GaReportGetUsersFromPageView($this->analytics,
            new Google_Service_AnalyticsReporting_GetReportsRequest(), $this->request,
            new Google_Service_AnalyticsReporting_DateRange(), [
                new Google_Service_AnalyticsReporting_Metric(),
                new Google_Service_AnalyticsReporting_Metric(),
            ], new Google_Service_AnalyticsReporting_Dimension(), new Google_Service_AnalyticsReporting_OrderBy(),
            new Google_Service_AnalyticsReporting_DimensionFilterClause());
    }
}

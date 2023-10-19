<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-11-03
 * Time: 12:54
 */

namespace App\Classes\Report;

use Google_Service_AnalyticsReporting_GetReportsResponse;

class GaReportGetPathPageViews extends GaReport
{
    public function getReport($path, $from = '2005-01-01', $to = 'today')
    {
        $this->init($path, $from, $to);

        return $this->format($this->get());
    }

    protected function format(Google_Service_AnalyticsReporting_GetReportsResponse $reports)
    {
        $out = $this->baseFormat($reports);

        return [
            'page_views' => $out->sum('pageviews'),
            'unique_page_views' => $out->sum('Unique Pageviews'),
        ];
    }

    protected function setMetrics(): void
    {
        // Init the Metrics object.
        $this->metrics[0]->setExpression('ga:pageviews');
        $this->metrics[0]->setAlias('pageviews');

        $this->metrics[1]->setExpression('ga:uniquePageviews');
        $this->metrics[1]->setAlias('Unique Pageviews');
    }

    protected function setDimension(): void
    {
        // Init the Dimension object.
        $this->dimension->setName('ga:pagePath');
    }

    protected function setOrderBy(): void
    {
        //set OrderBy
        $this->orderBy->setFieldName('ga:uniquePageviews');
    }

    /**
     * @param $path
     */
    protected function setDimensionFilter($path): void
    {
        $regEx = '^('.$path.')(\?.*|\/+\?.*|$|\/$)';
        $filter = [
            'dimension_name' => 'ga:pagePath',
            'operator' => 'REGEXP',
            // valid operators can be found here: https://developers.google.com/analytics/devguides/reporting/core/v4/rest/v4/reports/batchGet#FilterLogicalOperator
            'expressions' => $regEx,
        ];
//        dump($filter);
        $this->filter->setFilters($filter);
//        $this->filter->setOperator('EXACT');
    }
}

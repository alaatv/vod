<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-11-03
 * Time: 11:42
 */

namespace App\Classes\Report;

use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Dimension;
use Google_Service_AnalyticsReporting_DimensionFilterClause;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_GetReportsResponse;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_OrderBy;
use Google_Service_AnalyticsReporting_ReportRequest;
use Illuminate\Support\Collection;

abstract class GaReport implements ReportInterface
{
    /**
     * @var Google_Service_AnalyticsReporting_DateRange
     */
    protected $dateRange;

    /**
     * @var Google_Service_AnalyticsReporting_Metric[]
     */
    protected $metrics;

    /**
     * @var Google_Service_AnalyticsReporting_Dimension
     */
    protected $dimension;

    /**
     * @var Google_Service_AnalyticsReporting_OrderBy
     */
    protected $orderBy;

    /**
     * @var Google_Service_AnalyticsReporting_DimensionFilterClause
     */
    protected $filter;

    /**
     * @var Google_Service_AnalyticsReporting_ReportRequest
     */
    protected $request;

    /**
     * @var Google_Service_AnalyticsReporting_GetReportsRequest
     */
    protected $body;

    /**
     * @var Google_Service_AnalyticsReporting
     */
    protected $analytics;

    public function __construct(
        Google_Service_AnalyticsReporting $analytics,
        Google_Service_AnalyticsReporting_GetReportsRequest $body,
        Google_Service_AnalyticsReporting_ReportRequest $request,
        Google_Service_AnalyticsReporting_DateRange $dateRange,
        $metrics,
        Google_Service_AnalyticsReporting_Dimension $dimension,
        Google_Service_AnalyticsReporting_OrderBy $orderBy,
        Google_Service_AnalyticsReporting_DimensionFilterClause $filter
    ) {
        $this->dateRange = $dateRange;
        $this->metrics = $metrics;
        $this->dimension = $dimension;
        $this->orderBy = $orderBy;
        $this->filter = $filter;
        $this->request = $request;
        $this->body = $body;
        $this->analytics = $analytics;
    }

    abstract public function getReport($path, $from = '2013-01-01', $to = 'today');

    abstract protected function format(Google_Service_AnalyticsReporting_GetReportsResponse $reports);

    protected function get()
    {
        $this->body->setReportRequests([$this->request]);

        return $this->analytics->reports->batchGet($this->body);
    }

    protected function baseFormat(Google_Service_AnalyticsReporting_GetReportsResponse $reports): Collection
    {
        $darray = [];
        $marray = [];
        $mkey = [];

        for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
            $report = $reports[$reportIndex];
            $header = $report->getColumnHeader();
            $dimensionHeaders = $header->getDimensions();
            $metricHeaders = $header->getMetricHeader()
                ->getMetricHeaderEntries();
            $rows = $report->getData()
                ->getRows();

            for ($j = 0; $j < count($metricHeaders); $j++) {
                $entry = $metricHeaders[$j];
                $mkey[] = $entry->getName();
            }

            for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                $row = $rows[$rowIndex];
                $dimensions = $row->getDimensions();
                $metrics = $row->getMetrics();

                $darray[] = array_combine($dimensionHeaders, $dimensions);

                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    $marray[] = array_combine($mkey, $values);
                }
            }
        }

        $i = 0;
        $mdarray = [];
        foreach ($darray as $value) {
            $mdarray[] = array_merge($value, $marray[$i]);
            $i++;
        }

        $out = collect($mdarray);

        return $out;
    }

    /**
     * @param $path
     * @param $from
     * @param $to
     */
    protected function init($path, $from, $to): void
    {
        $this->setDataRange($from, $to);
        $this->setMetrics();
        $this->setDimension();
        $this->setOrderBy();
        $this->setDimensionFilter($path);
        $this->setRequest();
    }

    /**
     * @param $from
     * @param $to
     */
    protected function setDataRange($from, $to): void
    {
        // Init the DateRange object
        $this->dateRange->setStartDate($from);
        $this->dateRange->setEndDate($to);
    }

    abstract protected function setMetrics(): void;

    abstract protected function setDimension(): void;

    abstract protected function setOrderBy(): void;

    abstract protected function setDimensionFilter($value): void;

    protected function setRequest(): void
    {
        $this->request->setDateRanges($this->dateRange);
        $this->request->setMetrics($this->metrics);
        $this->request->setDimensions($this->dimension);
        $this->request->setOrderBys($this->orderBy);
        $this->request->setDimensionFilterClauses($this->filter);
    }
}

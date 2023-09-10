<?php

namespace Basics\Support\Http;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait SearchRequestFilters
{
    public function parentsFilterParam(): string {
        return 'parents';
    }

    public function dateStartFilterParam(): string {
        return 'start';
    }

    public function dateEndFilterParam(): string {
        return 'end';
    }

    public function searchFilterParam(): string {
        return 'search';
    }

    /**
     * Serialize the filter params on the request.
     *
     * @return array the request filter params
     * as `[$parentChain, $startDate, $endDate, $searchString]`
     */
    public function filterParams(Request $request)
    {
        // input gives priority to formValues: formValues + queryParams
        // instead give priority to queryParams: queryParams + formValues
        /** @var array */
        $params = $request->query() + $request->input();

        return [
            Arr::wrap(data_get($params, $this->parentsFilterParam())),
            $this->formatDate(data_get($params, $this->dateStartFilterParam())),
            $this->formatDate(data_get($params, $this->dateEndFilterParam())),
            data_get($params, $this->searchFilterParam()),
        ];
    }

    /**
     * Serialize Request param dates.
     */
    public function formatDate($date = null): ?Carbon
    {
        if (!is_string($date)) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $date);
    }
}

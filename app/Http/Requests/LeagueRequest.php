<?php

namespace App\Http\Requests;


class LeagueRequest extends AbstractRequest
{

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $validationData = [

            'playAll' => [
                'league_id' => ['required', 'exists:leagues,id', 'int']
            ],
            'nextWeek' => [
                'league_id' => ['required', 'exists:leagues,id', 'int'],
                'week' => 'required', 'int'
            ],
            'store' => [
                'groups' => ['array']
            ]
        ];

        $this->setValidationData($validationData);
    }
}

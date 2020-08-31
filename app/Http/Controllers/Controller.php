<?php

namespace App\Http\Controllers;

use Countable;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use function response;

class Controller extends BaseController
{

    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     *
     * @var Request
     */
    protected $request;

    public function __construct(EntityManager $em, Request $request)
    {
        $this->em = $em;
        $this->request = $request;
    }

    protected function stripTagsInputRequest($label): ?string
    {
        if ($this->request->input($label) === null) {
            return null;
        }
        return strip_tags($this->request->input($label));
    }
    protected function integerOfInputRequest($label): ?int
    {
        if ($this->request->input($label) === null) {
            return null;
        }
        return (int) $this->request->input($label);
    }
    protected function filterBooleanOfInputRequest($label): ?bool
    {
        if ($this->request->input($label) === null) {
            return null;
        }
        return filter_var($this->request->input($label), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
    
    protected function stripTagQueryRequest($label): ?string
    {
        if ($this->request->query($label) === null) {
            return null;
        }
        return strip_tags($this->request->query($label));
    }
    protected function integerOfQueryRequest($label): ?int
    {
        if ($this->request->query($label) === null) {
            return null;
        }
        return (int) $this->request->query($label);
    }
    protected function filterBooleanOfQueryRequest($label): ?bool
    {
        if ($this->request->query($label) === null) {
            return null;
        }
        return filter_var($this->request->query($label), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    protected function stripTagsVariable($var): ?string
    {
        return isset($var) ? strip_tags($var) : null;
    }
    protected function integerOfVariable($var): ?int
    {
        return isset($var) ? (int) $var : null;
    }
    protected function filterBooleanOfVariable($var): ?bool
    {
        return isset($var) ? filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
    }

    protected function commandCreatedResponse(array $result)
    {
        $content = [
            "data" => $result,
            "meta" => [
                "code" => 201,
                "type" => "Created",
            ],
        ];
        return response()->json($content, 201);
    }

    protected function commandOkResponse()
    {
        $content = [
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        return response()->json($content, 200);
    }

    protected function singleQueryResponse(array $result)
    {
        $content = [
            "data" => $result,
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ]
        ];
        return response()->json($content, 200);
    }

    protected function listQueryResponse(array $result)
    {
        $content = [
            "data" => $result,
            "meta" => [
                "code" => 200,
                "type" => "OK"
            ],
        ];
        return response()->json($content, 200);
    }

    protected function commonIdNameListQueryResponse(Countable $entityData)
    {
        $result = [];
        $result['total'] = count($entityData);
        foreach ($entityData as $datum) {
            $result["list"][] = [
                "id" => $datum->getId(),
                "name" => $datum->getName(),
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function getPage()
    {
        $page = (int) $this->request->query('page');
        return empty($page) ? 1 : $page;
    }

    protected function getPageSize()
    {
        $pageSize = (int) $this->request->query('pageSize');
//        $pageSize = filter_var($this->request->query('pageSize'), FILTER_SANITIZE_NUMBER_INT);
        $safeSize = $pageSize > 100 ? 100 : $pageSize;
        return empty($safeSize) ? 25 : $safeSize;
    }

}

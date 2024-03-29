<?php

namespace App\Http\Controllers;

use App\Jobs\Job;
use App\Jobs\SendImmediateMailJob;
use Countable;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Notification\Application\Service\SendImmediateMail;
use Notification\Domain\SharedModel\Mail\Recipient;
use Notification\Infrastructure\MailManager\SwiftMailSender;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Query\Domain\Service\DataFinder;
use Query\Infrastructure\QueryFilter\TimeIntervalFilter;
use Resources\PaginationFilter;
use SharedContext\Infrastructure\Persistence\Flysystem\FlysystemFileRepository;
use Swift_Mailer;
use Swift_SmtpTransport;
use Symfony\Component\HttpFoundation\Response;
use function env;
use function GuzzleHttp\json_encode;
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
        return $this->request->input($label);
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

    protected function dateTimeImmutableOfInputRequest($label): ?DateTimeImmutable
    {
        if ($this->request->input($label) === null) {
            return null;
        }
        return new DateTimeImmutable($this->request->input($label));
    }

    protected function stripTagQueryRequest($label): ?string
    {
        if ($this->request->query($label) === null) {
            return null;
        }
        return $this->request->query($label);
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

    protected function dateTimeImmutableOfQueryRequest($label): ?DateTimeImmutable
    {
        if ($this->request->query($label) === null) {
            return null;
        }
        return new DateTimeImmutable($this->request->query($label));
    }

    protected function stripTagsVariable($var): ?string
    {
        return isset($var) ? $var : null;
    }

    protected function integerOfVariable($var): ?int
    {
        return isset($var) ? (int) $var : null;
    }

    protected function floatOfVariable($var): ?float
    {
        return isset($var) ? (float) $var : null;
    }

    protected function filterBooleanOfVariable($var): ?bool
    {
        return isset($var) ? filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
    }
    
    protected function dateTimeImmutableOfVariable($var): ?DateTimeImmutable
    {
        return isset($var) ? new DateTimeImmutable($var) : null;
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

    protected function singleQueryResponse(?array $result)
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
    
    protected function sendAndCloseConnection(Response $response, Job $job): void
    {
        if (function_exists('fastcgi_finish_request')) {
//            $headers = ["Access-Control-Allow-Origin" => "*"];
            $response
//                    ->header('Access-Control-Allow-Origin', '*')
                    ->send();
            fastcgi_finish_request();
        } elseif (function_exists('litespeed_finish_request')) {
            $response
//                    ->header('Access-Control-Allow-Origin', '*')
                    ->send();
            litespeed_finish_request();
        } else {
            $headers = [
//                "Access-Control-Allow-Origin" => "*",
                "Connection" => "close\r\n",
                "Content-Encoding" => "none\r\n",
                "Content-Length" => strlen(json_encode($response->getContent())),
            ];
            $response
//                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Connection', 'close')
                    ->header('Content-Encoding', 'none')
                    ->header('Content-Length', strlen($response->content()))
                    ->send();
        }
        $job->handle();
    }
    
    protected function buildSendImmediateMailJob()
    {
        $recipientRepository = $this->em->getRepository(Recipient::class);
        return new SendImmediateMailJob($recipientRepository);
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
        $safeSize = $pageSize > 100 ? 100 : $pageSize;
        return empty($safeSize) ? 100 : $safeSize;
    }

    protected function buildSendImmediateMail(): SendImmediateMail
    {
        $recipientRepository = $this->em->getRepository(Recipient::class);
        $transport = new Swift_SmtpTransport(
                env('MAIL_SERVER_HOST'), env('MAIL_SERVER_PORT'), env('MAIL_SERVER_ENCRYPTION'));
        $transport->setUsername(env('MAIL_SERVER_USERNAME'));
        $transport->setPassword(env('MAIL_SERVER_PASSWORD'));
        $vendor = new Swift_Mailer($transport);
        $mailSender = new SwiftMailSender($vendor);
        return new SendImmediateMail($recipientRepository, $mailSender);
    }
    
    protected function sendImmediateMail(): void
    {
        $this->buildSendImmediateMail()->execute();
    }

    protected function getTimeIntervalFilter()
    {
        return (new TimeIntervalFilter)
                        ->setFrom($this->dateTimeImmutableOfQueryRequest("from"))
                        ->setTo($this->dateTimeImmutableOfQueryRequest("to"));
    }
    
    protected function buildDataFinder(): DataFinder
    {
        return new DataFinder($this->em->getConnection());
    }
    
    protected function executeImmediateSendMailExternally(): void
    {
        $sendmailPath = dirname(__DIR__, 3) . "/scripts/sendmail.php";
        exec("php $sendmailPath > /dev/null 2>/dev/null &");
    }
    
    function arrayPreserveJsOrder(array $data) {
        return array_map(
            function($key, $value) {
                if (is_array($value)) {
                    $value = $this->arrayPreserveJsOrder($value);
                }
                return array($key, $value);
            },
            array_keys($data),
            array_values($data)
        );
    }
    
    protected function sendXlsDownloadResponse(Xlsx $writer, string $filename = 'evaluation-report-summary.xls')
    {
        $callback = function() use($writer) {
            $file = fopen('php://output', 'w');
            $writer->save($file);
            fclose($file);
            return $file;
        };
        
        $headers = [
            "Content-type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        return response()->stream($callback, 200, $headers);
    }
    
    protected function buildFlysistemFileRepository()
    {
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        return new FlysystemFileRepository($filessystem);
    }
    
    protected function getPaginationFilter(): PaginationFilter
    {
        return new PaginationFilter($this->getPage(), $this->getPageSize());
    }
    
}

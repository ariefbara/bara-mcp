<?php
namespace Resources\Domain\Model\Mail;

class DynamicAttachment
{

    /**
     *
     * @var string
     */
    protected $fileName;

    /**
     *
     * @var string
     */
    protected $content;

    /**
     *
     * @var string
     */
    protected $contentType;

    public function __construct(string $fileName, string $content, ?string $contentType)
    {
        $this->fileName = $fileName;
        $this->content = $content;
        $this->contentType = $contentType;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}


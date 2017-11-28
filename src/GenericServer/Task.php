<?php

namespace App\GenericServer;

class Task
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var bool
     */
    private $closed;

    /**
     * @var string
     */
    private $issueUrl;

    /**
     * Task constructor.
     *
     * @param string $id
     * @param string $summary
     */
    public function __construct($id, $summary)
    {
        $this->id = $id;
        $this->summary = $summary;
    }

    public function setDescription(string $description): Task
    {
        $this->description = $description;

        return $this;
    }

    public function setUpdated(\DateTime $updated): Task
    {
        $this->updated = $updated;

        return $this;
    }

    public function setCreated(\DateTime $created): Task
    {
        $this->created = $created;

        return $this;
    }

    public function setClosed(bool $closed): Task
    {
        $this->closed = $closed;

        return $this;
    }

    public function setIssueUrl(string $issueUrl): Task
    {
        $this->issueUrl = $issueUrl;

        return $this;
    }

    public static function fromActiveCollab(array $data): self
    {
        $task = new self(
            $data['task_number'],
            sprintf(
                '%1$s (%2$s)',
                $data['name'],
                $data['project']['name']
            )
        );

        $task->setDescription(strip_tags($data['body']));
        $task->setUpdated(new \DateTime('@' . $data['updated_on']));
        $task->setCreated(new \DateTime('@' . $data['created_on']));
        $task->setClosed($data['is_completed']);
        $task->setIssueUrl($data['instance_url'] . $data['url_path']);

        return $task;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'summary' => $this->summary,
            'description' => $this->description,
            'updated' => $this->updated->format(\DateTime::ATOM),
            'created' => $this->created->format(\DateTime::ATOM),
            'closed' => $this->closed,
            'issueUrl' => $this->issueUrl,
        ];
    }
}

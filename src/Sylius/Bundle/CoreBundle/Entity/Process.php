<?php

namespace Sylius\Bundle\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\CoreBundle\Repository\ProcessRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ProcessRepository::class)
 */
class Process implements ResourceInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = Status::NOT_ACTIVE;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $processManagerId;

    /**
     * @ORM\Column(type="integer")
     */
    private $progress = 0;

    /**
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="process", cascade={"persist", "remove"})
     * @var Job[]
     */
    private $jobs;

    /**
     * @ORM\Column(type="json")
     */
    private $data = [];

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProgress(): ?int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): self
    {
        $this->progress = $progress;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setProcess($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->removeElement($job)) {
            if ($job->getProcess() === $this) {
                $job->setProcess(null);
            }
        }

        return $this;
    }

    public function getProcessManagerId()
    {
        return $this->processManagerId;
    }

    public function setProcessManagerId($processManagerId): self
    {
        $this->processManagerId = $processManagerId;

        return $this;
    }

    public function isFinished(): bool
    {
        $finishedAmount = $this->jobs->filter(function (Job $job): bool {
            return $job->getStatus() === Status::FINISHED;
        })->count();

        return $this->jobs->count() === $finishedAmount;
    }

    public function recalculateProgress(): void
    {
        $finishedAmount = $this->jobs->filter(function (Job $job): bool {
            return $job->getStatus() === Status::FINISHED;
        })->count();
        $progress = (int)floor(($finishedAmount / $this->jobs->count()) * 100);

        $this->progress = $progress;
    }
}

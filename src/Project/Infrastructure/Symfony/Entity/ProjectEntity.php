<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Src\Identity\Infrastructure\Symfony\Entity\UserEntity;
use Src\Project\Domain\Project;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;

#[ORM\Entity]
#[ORM\Table(name: 'projects')]
class ProjectEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    private string $slug;

    #[ORM\ManyToMany(targetEntity: UserEntity::class)]
    #[ORM\JoinTable(name: 'project_user')]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public static function fromDomain(Project $project): self
    {
        $entity = new self();
        $entity->id = $project->id()->value();
        $entity->name = $project->name();
        $entity->slug = $project->slug()->value();

        // Users will be handled in repository sync
        return $entity;
    }

    public function toDomain(): Project
    {
        /** @var string[] $userIds */
        $userIds = $this->users->map(fn (UserEntity $u) => $u->getId())->toArray();

        return new Project(
            new ProjectId($this->id),
            $this->name,
            new ProjectSlug($this->slug),
            $userIds
        );
    }
}

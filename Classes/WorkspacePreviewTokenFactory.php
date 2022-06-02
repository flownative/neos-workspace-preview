<?php
declare(strict_types=1);

namespace Flownative\WorkspacePreview;

use Flownative\TokenAuthentication\Security\Model\HashAndRoles;
use Flownative\TokenAuthentication\Security\Repository\HashAndRolesRepository;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Utility\Algorithms;

/**
 * Factory to quickly create tokens to preview specific workspaces.
 *
 * @Flow\Scope("singleton")
 */
class WorkspacePreviewTokenFactory
{
    /**
     * @Flow\Inject
     * @var HashAndRolesRepository
     */
    protected $hashAndRolesRepository;

    /**
     * Creates a token to preview the given workspace.
     *
     * @param string $workspaceName
     * @return HashAndRoles
     */
    public function create(string $workspaceName): HashAndRoles
    {
        $tokenMetadata = HashAndRoles::createWithHashRolesAndSettings(Algorithms::generateRandomString(64), ['Flownative.WorkspacePreview:WorkspacePreviewer'], ['workspaceName' => $workspaceName]);
        $this->hashAndRolesRepository->add($tokenMetadata);

        return $tokenMetadata;
    }

    /**
     * Refresh a preview token for a given workspace, so the hash is different afterwards.
     *
     * @param string $workspaceName
     * @return HashAndRoles
     */
    public function refresh(string $workspaceName): HashAndRoles
    {
        $this->removeExistingWorkspacePreviewFor($workspaceName);

        return $this->create($workspaceName);
    }

    /**
     * Remove all tokens to preview the given workspace.
     *
     * @param string $workspaceName
     * @return void
     */
    protected function removeExistingWorkspacePreviewFor(string $workspaceName): void
    {
        $allWorkspacePreviewTokens = $this->hashAndRolesRepository->findByRoles(['Flownative.WorkspacePreview:WorkspacePreviewer']);
        /** @var HashAndRoles $workspacePreviewToken */
        foreach ($allWorkspacePreviewTokens as $workspacePreviewToken) {
            $tokenWorkspaceName = $workspacePreviewToken->getSettings()['workspaceName'] ?? null;
            if ($tokenWorkspaceName === $workspaceName) {
                $this->hashAndRolesRepository->remove($workspacePreviewToken);
            }
        }
    }
}


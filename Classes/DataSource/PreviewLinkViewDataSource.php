<?php
namespace Flownative\WorkspacePreview\DataSource;

use Flownative\TokenAuthentication\Security\Model\HashAndRoles;
use Flownative\TokenAuthentication\Security\Repository\HashAndRolesRepository;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Mvc\Controller\ControllerContext;
use Neos\Flow\Mvc\Routing\Exception\MissingActionNameException;
use Neos\Neos\Service\DataSource\DataSourceInterface;

/**
 *
 */
class PreviewLinkViewDataSource implements DataSourceInterface
{
    /**
     * @var ControllerContext
     */
    protected $controllerContext;

    /**
     * @Flow\Inject
     * @var \Neos\Neos\Service\UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var HashAndRolesRepository
     */
    protected $hashAndRolesRepository;

    /**
     * @param ControllerContext $controllerContext
     */
    public function setControllerContext(ControllerContext $controllerContext)
    {
        $this->controllerContext = $controllerContext;
    }

    public static function getIdentifier()
    {
       return 'PreviewLinkView';
    }

    /**
     * @param NodeInterface|null $node
     * @param array $arguments
     * @return array|mixed
     * @throws MissingActionNameException
     */
    public function getData(NodeInterface $node = null, array $arguments)
    {
        if ($node === null) {
            return [
                'data' => [
                    'link' => '---'
                ]
            ];
        }

        return [
            'data' => [
                'link' => $this->getLink($node)
            ]
        ];
    }

    /**
     * @param NodeInterface $node
     * @return string
     */
    protected function getLink(NodeInterface $node): string
    {
        $personalWorkspace = $this->userService->getPersonalWorkspace();
        $baseWorkspace = $personalWorkspace->getBaseWorkspace();

        if ($baseWorkspace->isPublicWorkspace()) {
            return '---';
        }

        $hashAndRoles = $this->getHashTokenForWorkspace($baseWorkspace->getName());
        if ($hashAndRoles === null) {
            return 'No token';
        }

        $contextPath = str_replace('@' . $personalWorkspace->getName(), '@' . $hashAndRoles->getSettings()['workspaceName'], $node->getContextPath());

        return $this->controllerContext->getUriBuilder()->reset()->setCreateAbsoluteUri(true)
            ->uriFor('authenticate', [
            '_authenticationHashToken' => $hashAndRoles->getHash(),
            'node' => $contextPath
            ], 'HashTokenLogin', 'Flownative.WorkspacePreview', null);
    }

    /**
     * @param string $workspaceName
     * @return HashAndRoles
     */
    protected function getHashTokenForWorkspace(string $workspaceName): ?HashAndRoles
    {
        $possibleTokens = $this->hashAndRolesRepository->findByRoles(['Flownative.WorkspacePreview:WorkspacePreviewer']);

        $hashAndRoles = array_reduce($possibleTokens->toArray(), static function ($foundToken, HashAndRoles $currentToken) use ($workspaceName) {
            $currentWorkspaceName = $currentToken->getSettings()['workspaceName'] ?? null;
            if ($currentWorkspaceName === $workspaceName) {
                return $currentToken;
            }

            return $foundToken;
        }, null);

        return $hashAndRoles;
    }
}

<?php
namespace Flownative\WorkspacePreview\Controller;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Mvc\Controller\Argument;
use Neos\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use Neos\Neos\Domain\Service\ContentContext;
use Flownative\TokenAuthentication\Security\Repository\HashAndRolesRepository;

/**
 *
 */
class HashTokenLoginController extends AbstractAuthenticationController
{
    /**
     * @Flow\Inject
     * @var HashAndRolesRepository
     */
    protected $hashAndRolesRepository;

    /**
     * @Flow\Inject
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

    protected function errorAction()
    {
        return parent::errorAction();
    }

    /**
     * @return string
     */
    public function authenticateAction()
    {
        return parent::authenticateAction();
    }

    /**
     * @param ActionRequest|null $originalRequest
     */
    protected function onAuthenticationSuccess(ActionRequest $originalRequest = null)
    {
        $tokenHash = $this->request->getArgument('_authenticationHashToken');
        $token = $this->hashAndRolesRepository->findByIdentifier($tokenHash);
        if (!$token) {
            return;
        }

        $workspaceName = $token->getSettings()['workspaceName'] ?? '';
        if (empty($workspaceName)) {
            return;
        }

        $possibleNode = $this->getNodeArgumentValue();
        $this->redirectToWorkspace($workspaceName, $possibleNode);
    }

    /**
     * Get a possible node argument from the current request.
     *
     * @return NodeInterface|null
     */
    protected function getNodeArgumentValue()
    {
        if (!$this->request->hasArgument('node')) {
            return null;
        }

        $nodeArgument = new Argument('node', NodeInterface::class);
        $nodeArgument->setValue($this->request->getArgument('node'));
        return $nodeArgument->getValue();
    }

    /**
     * @param string $workspaceName
     * @param NodeInterface|null $nodeToRedirectTo
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     */
    protected function redirectToWorkspace(string $workspaceName, NodeInterface $nodeToRedirectTo = null)
    {
        /** @var ContentContext $context */
        $context = $this->contextFactory->create(['workspaceName' => $workspaceName]);

        $nodeInWorkspace = null;
        if ($nodeToRedirectTo instanceof NodeInterface) {
            $flowQuery = new FlowQuery([$nodeToRedirectTo]);
            $nodeInWorkspace = $flowQuery->context(['workspaceName' => $workspaceName])->get(0);
        }

        if ($nodeInWorkspace === null) {
            $nodeInWorkspace = $context->getCurrentSiteNode();
        }

        $this->redirect('show', 'Frontend\Node', 'Neos.Neos', ['node' => $nodeInWorkspace]);
    }
}

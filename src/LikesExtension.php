<?php

namespace Bolt\Extension\TwoKings\Likes;

use Bolt\Extension\DatabaseSchemaTrait;
use Bolt\Extension\SimpleExtension;
use Bolt\Extension\TwoKings\Likes\Model\Likes;
use Bolt\Extension\TwoKings\Likes\Table\LikesTable;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Likes extension class.
 *
 * @author Bob den Otter <bob@twokings.nl>
 * @author Xiao-Hu Tai <xiao@twokings.nl>
 */
class LikesExtension extends SimpleExtension
{
    use DatabaseSchemaTrait;

    /**
     * {@inheritdoc}
     */
    protected function registerServices(Application $app)
    {
        $this->extendDatabaseSchemaServices();
    }

    /**
     * {@inheritdoc}
     */
    protected function registerExtensionTables()
    {
        return [
            'likes' => LikesTable::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function registerFrontendRoutes(ControllerCollection $collection)
    {
        $collection->match('/async/likes', [ $this, 'likesController' ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function registerTwigFunctions()
    {
        return [
            'likes' => ['likesTwig', ['is_safe' => ['html']]]
        ];
    }

    /**
     *
     */
    public function likesTwig($record = null, $template = 'likes.twig')
    {
        $app = $this->getContainer();

        if (empty($record)) {
            $vars = $app['twig']->getGlobals();
            if (isset($vars['record'])) {
                $record = $this->record = $vars['record'];
            }
        }

        $likes = new Likes($app, $record->contenttype['slug'], $record['id']);

        $twigvars = [
            'record' => $record,
            'likes'  => $likes->getData(),
        ];

        return $this->renderTemplate($template, $twigvars);
    }

    /**
     *
     */
    public function likesController()
    {
        $app = $this->getContainer();
        $request = $app['request'];

        $type        = filter_var($request->request->get('type'), FILTER_SANITIZE_STRING);
        $contenttype = filter_var($request->request->get('contenttype'), FILTER_SANITIZE_STRING);
        $id          = intval($request->request->get('id'));
        $value       = intval($request->request->get('value'));

        $likes = new Likes($app, $contenttype, $id);

        $likes->set($type, $value);

        return 1;
    }

}

<?php

namespace YiiNodeSocket\Assets;

use Yii;
use yii\web\AssetBundle;

/**
 * @author Woody <Woody@HandBid.com>
 * @since 1.0
 */
class NodeSocketAssets extends AssetBundle
{

    public $sourcePath = '@nodeWeb';

    /**
     * Overridden by Setting the above attribute it
     * Forces Yii into using the asset caching library.
     *
      public $basePath = '@webroot';
      public $baseUrl = '@web';
     *
     */
    public $css = [
    ];
    public $js = [
    ];
    public $depends = [
    ];

    public function init()
    {
        $this->js[] = sprintf(
            "//%s:%d%s", Yii::$app->nodeSocket->host, Yii::$app->nodeSocket->port, '/socket.io/socket.io.js'
        );
        $this->js[] = 'client/client.js';
    }

}

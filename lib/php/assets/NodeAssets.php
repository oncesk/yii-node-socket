<?php
namespace YiiNodeSocket\Assets;

use Yii;
use yii\web\AssetBundle;

/**
 * @author Woody <Woody@HandBid.com>
 * @since 1.0
 */
class NodeAssets extends AssetBundle
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
    public $css = [];

    public $js = [
        'client.js'
    ];
    public $depends = [];
}

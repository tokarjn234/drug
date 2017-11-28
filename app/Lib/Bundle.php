<?php

namespace App\Lib;

class Bundle
{
    private static $bundleConfig = null;


    /**
     * Init bundles config
     * @return mixed|null
     * @throws \Exception
     */
    private static function initBundleConfig() {
        if (self::$bundleConfig !== null) {
            return self::$bundleConfig;
        }

        self::$bundleConfig = json_decode(file_get_contents(base_path('bundles.json')), true);

        if (! self::$bundleConfig) {
            throw new \Exception('Could not parse bundles.json');
        }

        return  self::$bundleConfig;
    }

    /**
     * Renders script bundle with given name
     * @param $name
     * @return string
     * @throws \Exception
     */
    public static function scripts($name) {
        $result = '';
        $bundlesConfig = self::initBundleConfig();

        if (empty ($bundlesConfig['scripts'][$name])) {
            throw new \Exception('Scripts bundles ' . $name . ' does not exist');
        }

        $bundle = $bundlesConfig['scripts'][$name];

        $outputFile = base_path($bundle['dir'] . '/' . $bundle['output']);

        if (!config('app.debug')) {

            if (!file_exists($outputFile)) {
                self::abortWithError();
            }

            $modified = md5(@filemtime($outputFile));

            $result = \HTML::script('js/' . $bundle['output'] . '?v=' . $modified);
        } else {
            @unlink($outputFile);

            foreach ($bundle['files'] as $file) {
                $result .=  \HTML::script('js/' . $file);
            }
        }

        return $result;
    }

    /**
     * Renders script bundle with given name
     * @param $name
     * @return string
     * @throws \Exception
     */
    public static function styles($name) {
        $result = '';
        $bundlesConfig = self::initBundleConfig();

        if (empty ($bundlesConfig['styles'][$name])) {
            throw new \Exception('Styles bundles ' . $name . ' does not exist');
        }

        $bundle = $bundlesConfig['styles'][$name];
        $outputFile = base_path($bundle['dir'] . '/' . $bundle['output']);

        if (!config('app.debug')) {

            if (!file_exists($outputFile)) {
                self::abortWithError();
            }

            $modified = md5(@filemtime($outputFile));

            $result = \HTML::style('css/' . $bundle['output'] . '?v=' . $modified);
        } else {
            @unlink($outputFile);

            foreach ($bundle['files'] as $file) {
                $result .=  \HTML::style('css/' . $file);
            }
        }

        return $result;
    }

    private static function abortWithError() {
        $msg = '<strong>Bundles checking failed</strong><br>Please build bundles in production mode by using Laravel elixir<br>Learn more at <a href="https://laravel.com/docs/5.1/elixir" target="_blank">https://laravel.com/docs/5.1/elixir</a>';
        ob_clean();
        $html = sprintf('<html><head><title>Errors</title></head><body><code>%s</code></body></html>', $msg);
        echo $html;
        exit;
    }
}
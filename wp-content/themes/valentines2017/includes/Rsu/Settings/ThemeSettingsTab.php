<?php

namespace Rsu\Settings;


/**
 * Theme Settings Tab
 */
class ThemeSettingsTab {
    protected $settings;

    public function __construct(ThemeSettings $settings, $tabs) {
        $this->settings = $settings;

        $this->render_form($tabs);
    }

    private function render_form($tabs) {
        array_map(function($tab){
            /**
             * Callback is render_this($tab) via the catch-all __call().
             */
            add_action( "wpts_tab_{$tab}", array( $this, "render_{$tab}" ) );
        }, $tabs);
    }

    private function render_this($form){
        array_map(function($setting){
            $this->render($setting);
        }, $this->settings->getSettings($form));
    }

    private function render($setting)
    {
        $name        = $setting['name'];
        $placeholder = $setting['placeholder'] ?: '';

        $savedValue = get_option($name);

        echo "<p><label for='$name'>" . $this->caption($name) . "</label><br>";
        echo '<input type="text" id="' . $name
            . '" name="' . $name
            . '" value="' . esc_attr( $savedValue )
            . '" placeholder="' . esc_attr($placeholder) . '" />';
        echo $placeholder ? "<br><span class='description'>$placeholder</span>" : '';
        echo "</p>";
    }

    private function caption($name)
    {
        return ucwords(implode(' ', array_slice(explode('_', $name), 2)));
    }

    public function __call($name, $arguments)
    {
        list($render, $form) = explode('_', $name, 2);

        if ($render == 'render') {
            $this->render_this($form);
        } else {
            trigger_error(sprintf('Call to undefined function: %s::%s().', get_class($this), $name), E_USER_ERROR);
        }
    }
}
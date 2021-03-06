<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\bootstrap4;

use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * ButtonDropdown renders a group or split button dropdown bootstrap component.
 *
 * For example,
 *
 * ```php
 * // a button group using Dropdown widget
 * echo ButtonDropdown::widget([
 *     'label' => 'Action',
 *     'dropdown' => [
 *         'items' => [
 *             ['label' => 'DropdownA', 'url' => '/'],
 *             ['label' => 'DropdownB', 'url' => '#'],
 *         ],
 *     ],
 * ]);
 * ```
 * @see http://getbootstrap.com/javascript/#buttons
 * @see http://getbootstrap.com/components/#btn-dropdowns
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 */
class ButtonDropdown extends Widget
{
    /**
     * @var string the button label
     */
    public $label = 'Button';
    /**
     * @var array the HTML attributes for the container tag. The following special options are recognized:
     *
     * - tag: string, defaults to "div", the name of the container tag.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var array the HTML attributes of the button.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $buttonOptions = [];
    /**
     * @var array the configuration array for [[Dropdown]].
     */
    public $dropdown = [];
    /**
     * @var bool whether to display a group of split-styled button group.
     */
    public $split = false;
    /**
     * @var string the tag to use to render the button
     */
    public $tagName = 'button';
    /**
     * @var bool whether the label should be HTML-encoded.
     */
    public $encodeLabel = true;
    /**
     * @var string name of a class to use for rendering dropdowns withing this widget. Defaults to [[Dropdown]].
     */
    public $dropdownClass = 'yii\bootstrap4\Dropdown';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        if (!isset($this->buttonOptions['id'])) {
            $this->buttonOptions['id'] = $this->options['id'] . '-button';
        }
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function run()
    {
        Html::addCssClass($this->options, ['widget' => 'dropdown']);
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        $html = implode("\n", [
            Html::beginTag($tag, $options),
            $this->renderButton(),
            $this->renderDropdown(),
            Html::endTag($tag)
        ]);

        // Set options id to button options id to ensure correct css selector in plugin initialisation
        $this->options['id'] = $this->buttonOptions['id'];

        $this->registerPlugin('dropdown');
        return $html;
    }

    /**
     * Generates the button dropdown.
     * @return string the rendering result.
     * @throws \Exception
     */
    protected function renderButton()
    {
        Html::addCssClass($this->buttonOptions, ['widget' => 'btn']);
        $label = $this->label;
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        if ($this->split) {
            $buttonOptions = $this->buttonOptions;
            $this->buttonOptions['data-toggle'] = 'dropdown';
            $this->buttonOptions['aria-haspopup'] = 'true';
            $this->buttonOptions['aria-expanded'] = 'true';
            Html::addCssClass($this->buttonOptions, ['toggle' => ['dropdown-toggle', 'dropdown-toggle-split']]);
            unset($buttonOptions['id']);
            $splitButton = Button::widget([
                'label' => '<span class="sr-only">Toggle Dropdown</span>',
                'encodeLabel' => false,
                'options' => $this->buttonOptions,
                'view' => $this->getView(),
            ]);
        } else {
            $buttonOptions = $this->buttonOptions;
            Html::addCssClass($buttonOptions, ['toggle' => 'dropdown-toggle']);
            $buttonOptions['data-toggle'] = 'dropdown';
            $buttonOptions['aria-haspopup'] = 'true';
            $buttonOptions['aria-expanded'] = 'false';
            $splitButton = '';
        }

        if (isset($buttonOptions['href'])) {
            if (is_array($buttonOptions['href'])) {
                $buttonOptions['href'] = Url::to($buttonOptions['href']);
            }
        } else {
            if ($this->tagName === 'a') {
                $buttonOptions['href'] = '#';
                $buttonOptions['role'] = 'button';
            }
        }

        return Button::widget([
                'tagName' => $this->tagName,
                'label' => $label,
                'options' => $buttonOptions,
                'encodeLabel' => false,
                'view' => $this->getView(),
            ]) . "\n" . $splitButton;
    }

    /**
     * Generates the dropdown menu.
     * @return string the rendering result.
     * @throws \Exception
     */
    protected function renderDropdown()
    {
        $config = $this->dropdown;
        $config['clientOptions'] = false;
        $config['view'] = $this->getView();
        /** @var Widget $dropdownClass */
        $dropdownClass = $this->dropdownClass;
        return $dropdownClass::widget($config);
    }
}

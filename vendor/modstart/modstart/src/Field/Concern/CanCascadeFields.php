<?php

namespace ModStart\Field\Concern;

use Illuminate\Support\Arr;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\Checkbox;
use ModStart\Field\Radio;
use ModStart\Field\Select;
use ModStart\Field\SwitchField;
use ModStart\Field\Type;
use ModStart\Form\Form;
use ModStart\ModStart;

/**
 * @mixin AbstractField
 * @property Form $context
 */
trait CanCascadeFields
{
    /**
     * @var array
     */
    protected $conditions = [];

    /**
     * @var array
     */
    protected $cascadeGroups = [];

    /**
     * @param $operator
     * @param $value
     * @param $closure \Closure function($builder) { }
     *
     * @return $this
     */
    public function when($operator, $value, $closure = null)
    {
        if (func_num_args() == 2) {
            $closure = $value;
            $value = $operator;
            $operator = $this->getDefaultOperator();
        }
        $this->formatValues($operator, $value);
        $this->addDependents($operator, $value, $closure);
        return $this;
    }

    protected function getDefaultOperator()
    {
        if ($this instanceof Checkbox) {
            return 'in';
        }
        return '=';
    }

    /**
     * @param string $operator
     * @param mixed $value
     */
    protected function formatValues($operator, &$value)
    {
        if (in_array($operator, ['in', 'notIn'])) {
            $value = json_encode($value);
        }
        if (is_array($value)) {
            $value = array_map('strval', $value);
        } else {
            $value = strval($value);
        }
    }

    /**
     * @param string $operator
     * @param mixed $value
     * @param \Closure $closure
     */
    protected function addDependents($operator, $value, \Closure $closure)
    {
        $index = count($this->conditions);
        $this->conditions[] = [
            'operator' => $operator,
            'value' => $value,
            'closure' => $closure,
            'index' => $index,
        ];
        $this->context->cascadeGroup($closure, [
            'id' => $this->id(),
            'index' => $index,
        ]);
    }

    /**
     * Add cascade scripts to contents.
     *
     * @return void
     */
    protected function addCascadeScript()
    {
        if (empty($this->conditions)) {
            return;
        }
        $cascadeGroups = collect($this->conditions)->map(function ($condition) use (&$index) {
            return ArrayUtil::keepKeys($condition, ['operator', 'value', 'index']);
        })->toJson();
        $id = $this->id();
        $script = <<<JS
(function () {
    var operatorTable = {
       'in': function(a, b) {
           b = JSON.parse(b); a = String(a);
           for(var i=0;i<b.length;i++){
               b[i] = String(b[i]);
           }
           return b.indexOf(a)>=0;
       },
       '=': function(a, b) {
           if ($.isArray(a) && $.isArray(b)) {
               return $(a).not(b).length === 0 && $(b).not(a).length === 0;
           }
           a = String(a); b = String(b);
           // console.log('operatorTable',a,b);
           var ab = [a,b].sort().join('|');
           if(ab==='1|true'||ab==='0|false'){
               return true;
           }
           return a === b;
       },
       '>': function(a, b) {
           return a > b;
       },
       '<': function(a, b) {
           return a < b;
       },
       '>=': function(a, b) { return a >= b; },
       '<=': function(a, b) { return a <= b; },
       '!=': function(a, b) {
            return ! operatorTable['='](a, b);
       }
   };
   var compare = function (a, b, o) {
       if(!(o in operatorTable)){
           console.error('unsupported operator ',o)
       }
       if (! $.isArray(b) ) return operatorTable[o](a, b);
       if (o === '!=') {
           for (var i in b) {
               if (! operatorTable[o](a, b[i])) return false;
           }
           return true;
       }
       for (var i in b) {
           if (operatorTable[o](a, b[i])) return true;
       }
       return false;
   };
   var cascadeGroups = $cascadeGroups;
   var cascadeChange = function(value){
       cascadeGroups.forEach(function (group) {
           var groupDom = $('#{$this->id()}_group_' + group.index);
           groupDom.addClass('cascade-group-hide');
           groupDom.find('input,textarea').prop('disabled',true)
       });
       cascadeGroups.forEach(function (group) {
           var groupDom = $('#{$this->id()}_group_' + group.index);
           var pass = compare(value, group.value, group.operator);
           // console.log(value, group.operator, group.value, pass);
           if (pass) {
               groupDom.removeClass('cascade-group-hide');
               groupDom.find('input,textarea').prop('disabled',false);
           }
        });
   };
   {$this->getFieldNormalizedScript()}
})();
JS;
        ModStart::script($script);
    }

    /**
     * @return string
     */
    protected function getFieldNormalizedScript()
    {
        if ($this->context instanceof Form) {
            switch (get_class($this)) {
                case Select::class:
                case Type::class:
                    return <<<JS
$('#{$this->id()} select').on('change', function (e) {
    cascadeChange($(this).val());
}).trigger('change');
JS;
                case Radio::class:
                    return <<<JS
$('#{$this->id()}').on('click', function (e) {
    cascadeChange($('#{$this->id()} [type=radio]:checked').val());
}).trigger('click');
JS;
                case Checkbox::class:
                    return <<<JS
$('#{$this->id()}').on('click', function (e) {
    var value = $('#{$this->id()} [type=checkbox]:checked').map(function(){
      return $(this).val();
    }).get();
    cascadeChange(value);
}).trigger('click');
JS;
                case SwitchField::class:
                    return <<<JS
$('#{$this->id()}').on('click', function (e) {
    cascadeChange($('#{$this->id()} [type=checkbox]').is(':checked')?1:0);
}).trigger('click');
JS;
                default:
                    throw new \InvalidArgumentException('Invalid form field type');
            }
        } else {
            $value = json_encode($this->value);
            switch (get_class($this)) {
                case Select::class:
                case Type::class:
                    return <<<JS
cascadeChange($value);
JS;
                case Radio::class:
                    return <<<JS
cascadeChange($value);
JS;
                case Checkbox::class:
                    return <<<JS
cascadeChange($value);
JS;
                case SwitchField::class:
                    return <<<JS
cascadeChange($value);
JS;

                default:
                    throw new \InvalidArgumentException('Invalid form field type');
            }
        }
    }
}

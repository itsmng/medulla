<?php

function renderInput($attributes) : string {
    $out = "";
    if ($attributes['type'] == 'checkbox') {
        $out = "<input class='form-check-input'";
        foreach ($attributes as $name => $value) {
            $out .= " $name='$value'";
        }
        $out .= "/>";
    } else if ($attributes['type'] == 'textarea') {
        $out = "<textarea class='form-control'";
        foreach ($attributes as $name => $value) {
            $out .= " $name='$value'";
        }
        $out .= "></textarea>";
    } else if ($attributes['type'] == 'select') {
        $out = "<select class='form-select'";
        foreach ($attributes as $name => $value) {
            $out .= " $name='$value'";
        }
        $out .= ">";
        if (isset($attributes['values'])) {
            foreach ($attributes['values'] as $key => $item) {
                $out .= "<option value='$key'";
                if ($attributes['value'] == $key) {
                    $out .= " selected='selected'";
                }
                $out .= ">$item</option>";
            }
        }
        $out .= "</select>";
    } else {
        $out = "<input class='form-control'";
        foreach ($attributes as $name => $value) {
            $out .= " $name='$value'";
        }
        $out .= "/>";
    }
    return $out;
}

function renderForm($form, $additionnalHtml = '', $col = 2) : string {
    $method = $form['method'] ?? 'post';
    $options = ['actions', 'after', 'before', 'hooks'];
    $out = <<<HTML
    <form name="form" action="{$form['action']}" enctype="multipart/form-data" method="$method" class="container">
    HTML;
    foreach ($form['content'] as $key => $bloc) {
        $out .= <<<HTML
        <div class="container mb-3">
        HTML;
        if ($bloc['visible']) {
            $out .= <<<HTML
            <h2 class="text-start">$key</h2>
            HTML;
        }
        $out .= <<<HTML
        <div class="row row-cols-{$col}">
        HTML;
        foreach ($bloc['inputs'] as $title => $input) {
            if (isset($input['type']) && $input['type'] != 'hidden') {
                $out .= <<<HTML
                <div class="col col-lg-4 col-md-6 col-12 text-start">
                    <label for="{$input['name']}">$title</label>
                    <div class="d-flex justify-content-between align-items-center btn-group my-1">
                HTML;
            }
            $attributes = array_filter($input, function($k) use ($options) {
                return !in_array($k, $options);
            }, ARRAY_FILTER_USE_KEY);
            if (isset($input['type'])) {
                $out .= renderInput($attributes);
            } else {
                $out .= $input['content'];
            }
            if (isset($input['after'])) {
                $out .= <<<HTML
                <div class="form-text mx-2">{$input['after']}</div>
                HTML;
            }
            if (isset($input['actions'])) {
                foreach (($input['actions']) as $action) {
                    $out .= <<<HTML
                    <button type="button" class="btn border" onClick="{$action['onClick']}"><a class="{$action['icon']}"></a></button>
                    HTML;
                }
            }
            if (isset($input['type']) && $input['type'] != 'hidden') {
                $out .= <<<HTML
                    </div>
                </div>
                HTML;
            }
        }
        $out .= <<<HTML
        </div>
        </div>
        HTML;
    }
    $out .= <<<HTML
    $additionnalHtml
    HTML;
    $out .= <<<HTML
    <div class="d-flex justify-content-around">
    HTML;
    if (!isset($form['buttons'])) {
        $out .= <<<HTML
            <button type="submit" class='btn btn-primary'>Submit</button>
        HTML;
    } else {
        foreach ($form['buttons'] as $button) {
            $out .= <<<HTML
            <button 
            HTML;
            foreach ($button as $name => $value) {
                $out .= "$name='$value'";
            }
            $out .= <<<HTML
            >{$button['value']}</button>
            HTML;
        }
    }
    $out .= <<<HTML
    </div>
    </form>
    HTML;

    // SCRIPTS
    $out .= <<<HTML
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
    HTML;
    foreach ($form['content'] as $bloc) {
        foreach ($bloc['inputs'] as $input) {
            if (isset($input['init'])) {
                $out .= $input['init'];
            }
            if (isset($input['hooks'])) {
                foreach ($input['hooks'] as $hook => $script) {
                    $out .= <<<HTML
                    $("#{$input['id']}").on("{$hook}", function() {
                        {$script}
                    })
                    HTML;
                }
            }
        }
    }
    $out .= <<<HTML
    </script>
    HTML;
    
    return $out;
}
?>
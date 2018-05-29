<?php

require 'vendor/autoload.php';

use MatthiasMullie\Minify;


$js = array(
    'public/js/Common.js',
    'public/js/ux/Ext.ux.RadWizard.js',
    'public/js/System.js',
    'public/js/PubSub.js',
    'public/js/Desktop.js',
    'public/js/StartMenu.js',
    'public/js/TaskBar.js',
    'public/js/Shortcut.js',
    'public/js/ux/renderers/basic.js',
    'public/js/ux/form/xcombo.js',
    'public/js/ux/form/maskfieldplugin.js',
    'public/js/ux/form/xdatefield.js',
    'public/js/ux/form/xcheckbox.js',
    'public/js/ux/Ext.ux.RadForm.js',
    'public/js/ux/grid/GridSummary.js',
    'public/js/ux/Ext.ux.autogrid.js',
    'public/js/ux/form/datetime.js',
    'public/js/ux/Ext.ux.IFrame.js',
    'public/js/ux/grid/CheckColumn.js',
    'public/js/ux/Ext.ux.RadMap.js',
    'public/js/ux/DragSelector.js',
    'public/js/ux/grid/filter/Filter.js',
    'public/js/ux/grid/filter/StringFilter.js',
    'public/js/ux/grid/menu/RangeMenu.js',
    'public/js/ux/grid/filter/NumericFilter.js',
    'public/js/ux/grid/menu/ListMenu.js',
    'public/js/ux/grid/filter/ListFilter.js',
    'public/js/ux/grid/GridFilters.js',
    'public/js/ux/grid/filter/DateFilter.js',
    'public/js/ux/grid/filter/BooleanFilter.js',
    'public/js/ux/grid/RowEditor.js',
    'public/js/ux/menu/TreeMenu.js',
    'public/js/ux/menu/RangeMenu.js',
    'public/js/ux/form/radtemplates.js',
    'public/js/ux/form/FileUploadField.js',
    'public/js/ux/menu/ListMenu.js',
    'public/js/ux/grid/GroupSummary.js',
    'public/js/ux/Ext.ux.RadTree.js',
    'public/js/ux/menu/EditableItem.js',
    'public/js/ux/form/advcombo.js',
    'public/js/ux/Ext.ux.StatusBar.js',
    'public/js/ux/Ext.ux.form.LinkTriggerField.js',
    'public/js/erp/resaltador.js',
    'public/js/erp/depositos.js',
    'public/js/ux/Reorderer.js',
    'public/js/ux/Ext.ux.RadRemoteProvider.js',
    'public/js/ux/ToolbarDroppable.js',
    'public/js/ux/ToolbarReorderer.js',
    'public/js/ux/Ext.ux.form.SuperBoxSelect.js',
    'public/js/ux/Ext.ux.PanelCollapsedTitle.js',
    'public/js/erp/depositopanel.js',
    'public/js/ux/Ext.ux.RadParameterTable.js',
    'public/js/ux/Ext.ux.Wamp.js'
);

$minifier = new Minify\JS();

foreach ($js as $file) {
    $minifier->add($file);
}

$minifier->minify('public/js/all.js');

echo 'Js Minified'.PHP_EOL;
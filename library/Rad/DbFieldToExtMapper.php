<?php

/**
 * Esta clase mapea la configuracion de los Campos editores de Extjs
 * a partir de la matadata de Zend_Db_Table
 *
 * @author Martin A. Santangelo
 */
class Rad_DbFieldToExtMapper
{
    protected static $_dataTypeToColumnType = array(
         'tinyint'   => 'int',
         'bigint'    => 'string',
         'int'       => 'string', // va string pq sino me convierte los null a 0 en los stores
         'decimal'   => 'string', // va string pq sino me convierte los null a 0 en los stores
         'timestamp' => 'date',
         'datetime'  => 'date',
         'date'      => 'date',
         'time'      => 'date',
         'text'      => 'string',
         'varchar'   => 'string'
    );


    public static function getType($datatipe)
    {
        $rtn = self::$_dataTypeToColumnType[$datatipe];
        return ($rtn)?$rtn:'string';
    }


    /**
     * Retorna la Metadata para la configuracion del AutoGrid dada la MetaData de Zend_Db_Table
     *
     * @param array $metaData
     * @return array
     */
    public static function getAutoGridMetaDataFromField($field, &$metaData, $editor = true, $js = true)
    {
        $fieldMetadata['type'] = self::getType($metaData['DATA_TYPE']);
        // Tipo de campo
        switch ($metaData['DATA_TYPE']) {
            case 'tinyint':
                //$fieldMetadata['isBoolean'] = true;

                $fieldMetadata['xtype']     = 'booleancolumn';
                $fieldMetadata['width']     = 50;
                $fieldMetadata['align']     = 'right';
                $fieldMetadata['trueText']  = 'Si';
                $fieldMetadata['falseText'] = 'No';

                break;
            case 'bigint':
            case 'int':
                $fieldMetadata['width'] = 50;
                $fieldMetadata['align'] = 'right';

                break;
            case 'decimal':
                // No se pq tenia string se cambio a float (Martin)
                $fieldMetadata['width']      = 80;
                $fieldMetadata['align']      = 'right';

                break;
            case 'timestamp':
            case 'datetime':
                $fieldMetadata['dateFormat'] = 'Y-m-d H:i:s';
                $fieldMetadata['width']      = 200;
                if ($js) {
                    $fieldMetadata['renderer'] = new Zend_Json_Expr("Ext.util.Format.dateRenderer('d-m-Y H:i')");
                }
                break;
            case 'time':
                $fieldMetadata['dateFormat'] = 'H:i:s';
                $fieldMetadata['width']      = 200;
                if ($js) {
                    $fieldMetadata['renderer'] = new Zend_Json_Expr("Ext.util.Format.dateRenderer('H:i')");
                }
                break;
            case 'date':
                $fieldMetadata['xtype']      = 'datecolumn';
                $fieldMetadata['dateFormat'] = 'Y-m-d';
                $fieldMetadata['format']     = 'd/m/Y';
                //if ($js) $fieldMetadata['renderer']   = new Zend_Json_Expr("Ext.util.Format.dateRenderer('d/m/Y')");
                break;
            case 'text':
                $fieldMetadata['width'] = 250;
                break;
            case 'varchar':
                $fieldMetadata['width'] = 5 * $metaData['LENGTH'];
                if ($fieldMetadata['width'] > 250) {
                    $fieldMetadata['width'] = 250;
                } else if ($fieldMetadata['width'] < 50) {
                    $fieldMetadata['width'] = 50;
                }
                //$fieldMetadata['renderer'] = new Zend_Json_Expr("Ext.util.Format.ellipsis(50)");

                break;
            default:
                $fieldMetadata['width'] = 60;
        }

        $fieldMetadata['name']      = $field;
        $fieldMetadata['groupable'] = false;
        if ($metaData['COLUMN_NAME']) {
            $fieldMetadata['header']    = ucfirst($metaData['COLUMN_NAME']);
        } else {
            $fieldMetadata['header']    = null;
        }


        if ((!isset($metaData['JOINED_FIELD']) || !$metaData['JOINED_FIELD'])) {
            if ($editor) {
                $fieldMetadata['editor'] = self::getFieldConfig($metaData, true);
                //$fieldMetadata['css']    = "background-color: #f0f1f8;";
            }
            // Si es un combo le personalisamos el renderer para que muestre la descripcion en vez del valor
            if (isset($metaData['COMBO_SOURCE'])) {
                $fieldMetadata['type'] = 'string';
            }

            if (isset($metaData['COMBO_SOURCE']) && $js) {
                if ($metaData['REL_LINK']) {
                    $fieldMetadata['renderer'] = new Zend_Json_Expr("function(v, params, record) {
                            var onClk = 'ondblclick=\"app.publish(app.channels.apps + \'{$metaData['REL_LINK']}\', {action:\'find\', value: '+v+'});var e = arguments[0];e.stopPropagation();\"';
                      		var a= '<div  style=\"background-image:url(images/link.png)\" qtip=\"Ver\" class=\"ux-cell-action \"> </div>';
                      		return '<span '+onClk+' class=\"model-link\">'+a+'</span>'+record.data.{$metaData['COLUMN_NAME']}_cdisplay;
                    }");
                } else {
                    $fieldMetadata['renderer'] = new Zend_Json_Expr("function(v, params, record) {
                      		return record.data.{$metaData['COLUMN_NAME']}_cdisplay;
                      }");
                }
            }

        } else {
            if (strpos($field,'_cdisplay') !== false) {
                unset($fieldMetadata['header']);        // Este registro solo queda en el data source es ignorado por la grilla
            }
        }

        $fieldMetadata['dataIndex'] = $field;

        if ($metaData['PRIMARY']) {
            $fieldMetadata['hidden'] = true;
            $fieldMetadata['filter']['type'] = 'numeric';

        }
        $fieldMetadata['sortable'] = true;
        return $fieldMetadata;
    }

    /**
     * Retorna la Metadata para la configuracion del AutoGrid dada la MetaData de Zend_Db_Table
     *
     * @param array $metaData
     * @return array
     */
    public static function getMetaDataFromNoDbField($field, &$metaData, $editor = true, $js = true)
    {
        $rtn = self::getAutoGridMetaDataFromField($field, $metaData, $editor, $js);
        $rtn['sortable'] = false;
        $rtn['editable'] = false;
        return $rtn;
    }

    /**
     * Retorna la configuracion de un Campo Ext.form.Field segun los metadats de un campo de la base
     *
     * @param array $metaData
     * @return array
     */
    public static function getFieldConfig($metaData, $isGrid = false)
    {
        // Es un combo?
        if (isset($metaData['COMBO_SOURCE'])) {
            // muestra el primer campo joineado (Q en el array es el ultimo pq se borra y se vuelve a insertar)
            $displayField = end($metaData['JOINED_COLUMNS']);

            $config = array(
                'xtype'          => 'xcombo',
                'width'          => 120,
                'minChars'       => 3,
                'displayField'   => $displayField,
                'autoLoad'       => true,
                'autoSelect'     => true,
                'selectOnFocus'  => true,
                'forceSelection' => true,
                'forceReload'    => true,
                'hiddenName'     => $metaData['COLUMN_NAME'],
                'loadingText'    => 'Cargando...',
                //'minListWidth' => 220,
                'lazyRender'     => true, //should always be true for editor
                'searchField'    => $displayField, // este es el campo por el que buscara
                'typeAhead'      => false,
                'valueField'     => $metaData['JOIN_REF_COLUMNS'][0],
                'store' => array(
                    'storeType' => 'JsonStore', //will do new Ext.data.SimpleStore({}) on client
                    'config'    => array(
                        'id'      => 0, //array index of the record id.
                        'url'     => $metaData['COMBO_SOURCE'] . ($metaData['COMBO_FILTER'] ? "/fqfield/" . $metaData['COMBO_FILTER'] : ""),
                        'storeId' => $metaData['COLUMN_NAME'] . 'Store',
                    )
                )
            );

            if ($metaData['COMBO_PAGESIZE']) {
                $config['pageSize'] = $metaData['COMBO_PAGESIZE'];
                $config['editable'] = true;
                $config['width'] = 220;
            }

            // No uso auto load pq al crear el formulario todabia no tengo los valores, y si el combo es paginado necesito el valor para traer
            // la descripcion ya que puede no estar en la misma pagina.
            if (!$isGrid) {
                $config['autoLoad'] = false;
                $config['autocomplete'] = true;
            }
        } else {
            switch ($metaData['DATA_TYPE']) {
                case 'bigint':
                case 'int':
                    $config['xtype'] = 'numberfield';
                    $config['allowDecimals'] = false;
                    break;
                case 'decimal':
                    $config['xtype'] = 'numberfield';
                    $config['decimalPrecision'] = $metaData['SCALE'];
                    break;
                case 'date':
                    $config['xtype'] = 'xdatefield';            // Uso el XDateField pq tiene soporte para 2 formatos uno para mostrar y el otro para el submit del formulario (El origianl no lo tiene)
                    $config['format'] = 'd/m/Y';
                    //$config['dateFormat'] = 'Y-m-d';
                    $config['value'] = '';
                    break;
                case 'time':
                    $config['xtype']  = 'timefield';
                    $config['format'] = 'H:i';
                    break;
                case 'timestamp':
                case 'datetime':
                    $config['xtype'] = 'xdatetime';
                    $config['timeFormat'] = 'H:i:s';
                    $config['dateFormat'] = 'd/m/Y';
                    break;
                case 'tinyint':
                    if ($isGrid) {
                        $config['xtype']         = 'combo';
                        $config['typeAhead']     = true;
                        $config['triggerAction'] = 'all';
                        $config['lazyRender']    = true;
                        $config['mode']          = 'local';
                        $config['valueField']    = 'Id';
                        $config['displayField']  = 'Text';
                        $config['store']         = array(
                            'storeType' => 'ArrayStore',
                            'config'    => array(
                                'id'     => 0,
                                'fields' => array('Id', 'Text'),
                                'data'   => array(array(0, 'No'), array(1, 'Si')),
                            )
                        );
                    } else {
                        $config['xtype'] = 'xcheckbox';
                        $config['width'] = '50';
                    }

                    break;
                case 'varchar':
                    $config['xtype'] = 'textfield';
                    $config['autoCreate'] = array(
                        'tag'          => 'input',
                        'type'         => 'text',
                        //'size'       => '20',
                        'autocomplete' => 'off',
                        'maxlength'    => $metaData['LENGTH']
                    );
                    break;
                case 'text':
                    $config['xtype'] = 'htmleditor';
                    //$config['autoSize'] = 'none';
                    $config['width'] = 540;
                    $config['height'] = 110;
                    break;
                default:
                    $config['xtype'] = 'textfield';

                    $config['maxLengthText'] = "Maximo " . $metaData['LENGTH'] . " caracteres";
                    $config['maxLength'] = $metaData['LENGTH'];
            }
        }
        if ($metaData['NULLABLE'] && !$metaData['PRIMARY']) {
            $config['allowBlank'] = true;
        } else {
            $config['allowBlank'] = false;
        }
        if ($metaData['UNSIGNED']) {
            $config['allowNegative'] = false;
        }
        if ($metaData['SCALE']) {
            $config['decimalPrecision'] = $metaData['PRECISION'];
        }
        $config['fieldLabel'] = ucfirst($metaData['COLUMN_NAME']);
        $config['name'] = $metaData['COLUMN_NAME'];
        //$config['msgTarget'] =  'side';
        return $config;
    }

}

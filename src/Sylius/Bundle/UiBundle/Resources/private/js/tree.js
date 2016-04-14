(function($) {
    $(document).ready(function() {
        $('#tree').jstree({
            'core' : {
                "check_callback" : true,
                "themes" : { "stripes" : true },
                'data' : {
                    'url' : function (node) {
                        return node.id === '#' ?
                            '/admin/taxons/' : '/admin/taxons/';
                    },
                    'data' : function (node) {
                        return { "id" : node.id };
                    }
                }
            },
            'contextmenu' : {
                "items": function(node) {
                    var tree = $("#tree").jstree(true);
                    return {
                        "Create": {
                            "separator_before": false,
                            "separator_after": false,
                            "label": "Nowa kategoria",
                            "action": function (obj) {
                                node = tree.create_node(node);
                            }
                        },
                        "Rename": {
                            "separator_before": false,
                            "separator_after": false,
                            "label": "Edytuj",
                            "action": function (obj) {
                                tree.edit(node);
                            }
                        },
                        "Remove": {
                            "separator_before": false,
                            "separator_after": false,
                            "label": "Usuń",
                            "action": function (obj) {
                                if ('#' === node.parent) {
                                    Materialize.toast('Nie można usunąć kategorii '+node.text+', ponieważ jest to główna kategoria', 5000)
                                    return false;
                                }
                                tree.delete_node(node);
                            }
                        }
                    };
                }
            },
            'types' : {
                'default' : { 'icon' : 'none' }
            },
            "plugins" : [ 'contextmenu', 'wholerow', 'types' ]
        });
    });
})(jQuery);

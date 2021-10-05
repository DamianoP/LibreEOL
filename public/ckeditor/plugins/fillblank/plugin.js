CKEDITOR.plugins.add( 'fillblank', {
    icons: 'fillblank',
    init: function( editor ) {
        //Plugin logic goes here.
        var maxIdAtInit;
        var data=editor.getData();
        var matching=data.match(/(?<=\<input data-id=.@==@=@==@)(.*?)(?=@==@=@==@. name=.fb_item. type=.text. \/>)/g);
        if (matching){
            if (matching.length > 1) {
                var length=matching.length;
                maxIdAtInit=matching[0];
                for (let i = 1; i < length; i++) {
                    if (matching[i] > maxIdAtInit)
                        maxIdAtInit = matching[i];
                }
            } else {
                maxIdAtInit = matching[0];
            }
        }else {
            maxIdAtInit=0;
        }

        editor.addCommand( 'insertFillBlank', {
            exec: function( editor ) {
                maxIdAtInit++;
                editor.insertHtml( ' <input data-id="@==@=@==@'+maxIdAtInit+'@==@=@==@" name="fb_item" type="text">','unfiltered_html');
            }
        });
        editor.ui.addButton( 'FillBlank', {
            label: 'Fill Blank Item',
            command: 'insertFillBlank',
            toolbar: 'insert'
        });
    }
});


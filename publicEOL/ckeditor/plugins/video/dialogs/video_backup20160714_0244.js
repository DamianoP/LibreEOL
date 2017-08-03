CKEDITOR.dialog.add( 'video', function ( editor )
{
	var lang = editor.lang.video;
	var videoId;
	var numberOfPlays=2;
	function commitValue( videoNode, extraStyles )
	{
		var value=this.getValue();

		if ( !value && this.id=='id' )
			value = generateId();

		if ( !value )
			return;
		switch( this.id )
		{
			case 'poster':
				videoNode.setAttribute( this.id, value);
				extraStyles.backgroundImage = 'url(' + value + ')';
				break;
			case 'width':
				videoNode.setAttribute( this.id, value);
				extraStyles.width = value + 'px';
				break;
			case 'height':
				videoNode.setAttribute( this.id, value);
				extraStyles.height = value + 'px';
				break;
			case 'numberOfPlays':
				numberOfPlays=value;
				break;
		}
	}

	function commitSrc( videoNode, extraStyles, videos )
	{
		var match = this.id.match(/(\w+)(\d)/),
			id = match[1],
			number = parseInt(match[2], 10);

		var video = videos[number] || (videos[number]={});
		video[id] = this.getValue();
	}

	function loadValue( videoNode )
	{
		if ( videoNode )
			this.setValue( videoNode.getAttribute( this.id ) );
		else
		{
			if ( this.id == 'id')
				this.setValue( generateId() );
		}
	}

	function loadSrc( videoNode, videos )
	{
		var match = this.id.match(/(\w+)(\d)/),
			id = match[1],
			number = parseInt(match[2], 10);

		var video = videos[number];
		if (!video)
			return;
		this.setValue( video[ id ] );
	}

	function generateId()
	{
		var now = new Date();
		videoId='video' + now.getFullYear() + now.getMonth() + now.getDate() + now.getHours() + now.getMinutes() + now.getSeconds();
		return videoId;
	}

	// To automatically get the dimensions of the poster image
	var onImgLoadEvent = function()
	{
		// Image is ready.
		var preview = this.previewImage;
		preview.removeListener( 'load', onImgLoadEvent );
		preview.removeListener( 'error', onImgLoadErrorEvent );
		preview.removeListener( 'abort', onImgLoadErrorEvent );

		this.setValueOf( 'info', 'width', preview.$.width );
		this.setValueOf( 'info', 'height', preview.$.height );
	};

	var onImgLoadErrorEvent = function()
	{
		// Error. Image is not loaded.
		var preview = this.previewImage;
		preview.removeListener( 'load', onImgLoadEvent );
		preview.removeListener( 'error', onImgLoadErrorEvent );
		preview.removeListener( 'abort', onImgLoadErrorEvent );
	};

	return {
		title : lang.dialogTitle,
		minWidth : 400,
		minHeight : 200,

		onShow : function()
		{
			// Clear previously saved elements.
			this.fakeImage = this.videoNode = null;
			// To get dimensions of poster image
			this.previewImage = editor.document.createElement( 'img' );

			var fakeImage = this.getSelectedElement();
			if ( fakeImage && fakeImage.data( 'cke-real-element-type' ) && fakeImage.data( 'cke-real-element-type' ) == 'video' )
			{
				this.fakeImage = fakeImage;

				var videoNode = editor.restoreRealElement( fakeImage ),
					videos = [],
					sourceList = videoNode.getElementsByTag( 'source', '' );
				if (sourceList.count()==0)
					sourceList = videoNode.getElementsByTag( 'source', 'cke' );

				for ( var i = 0, length = sourceList.count() ; i < length ; i++ )
				{
					var item = sourceList.getItem( i );
					videos.push( {src : item.getAttribute( 'src' ), type: item.getAttribute( 'type' )} );
				}

				this.videoNode = videoNode;

				this.setupContent( videoNode, videos );
			}
			else
				this.setupContent( null, [] );
		},

		onOk : function()
		{
			// If there's no selected element create one. Otherwise, reuse it
			var videoNode = null;
			if ( !this.fakeImage )
			{
				videoNode = CKEDITOR.dom.element.createFromHtml( '<cke:video id='+videoId+'></cke:video>', editor.document );
				
			}
			else
			{
				videoNode = this.videoNode;
			}

			var extraStyles = {}, videos = [];
			this.commitContent( videoNode, extraStyles, videos );

			var innerHtml = '', links = '',
				link = lang.linkTemplate || '',
				fallbackTemplate = lang.fallbackTemplate || '';
			for(var i=0; i<videos.length; i++)
			{
				var video = videos[i];
				if ( !video || !video.src )
					continue;
				innerHtml += '<cke:source src="' + video.src + '" type="' + video.type + '" />';
				links += link.replace('%src%', video.src).replace('%type%', video.type);
			}
			videoNode.setHtml( innerHtml + fallbackTemplate.replace( '%links%', links ) );

			
			// Refresh the fake image.
			var newFakeImage = editor.createFakeElement( videoNode, 'cke_video', 'video', false );
			newFakeImage.setStyles( extraStyles );
			if ( this.fakeImage )
			{
				newFakeImage.replace( this.fakeImage );
				editor.getSelection().selectElement( newFakeImage );
			}
			else
			{
				// Insert it in a div
				var div = new CKEDITOR.dom.element( 'DIV', editor.document );
				editor.insertElement( div );
				div.append( newFakeImage );
			}
			div.append( CKEDITOR.dom.element.createFromHtml('<script>'+
						'var riprodotto'+videoId+'=0;'+
						'document.getElementById("'+videoId+'").addEventListener("ended",controlloRiproduzioneCompletata,false);'+
						'function controlloRiproduzioneCompletata(e){'+
							'riprodotto'+videoId+'=riprodotto'+videoId+'+1;'+
						'}</script>') );
			div.append( CKEDITOR.dom.element.createFromHtml('<style>'+
				'video{pointer-events: none;}'+
				' .'+videoId+'_button{'+
      			' background-color: #4CAF50;border: none;color: white;padding: 15px;'+
    			' text-align: center;text-decoration: none;display: inline-block;font-size: 16px;'+
	     		' cursor: pointer;border-radius: 8px;box-shadow: 0 5px #666;outline: none;} .'+videoId+'_button:hover { '+
	    ' background-color: red;color: white;box-shadow: 0 5px #666;} .'+videoId+'_button:active {background-color: red;box-shadow: 0 1px #666;transform: translateY(4px);'+
'</style>') );
			div.append( CKEDITOR.dom.element.createFromHtml('<div>'+
				' <button class="'+videoId+'_button" onclick="'+videoId+'_playPause()">Riproduci</button>'+
				' <button class="'+videoId+'_button" onclick="'+videoId+'_ingrandisci()">Ingrandisci</button>'+
				' <button class="'+videoId+'_button" onclick="'+videoId+'_normale()"> Dimensione Normale</button>'+
				'</div>') );
			div.append( CKEDITOR.dom.element.createFromHtml('<script>'+
				'function '+videoId+'_playPause(){'+
					'if (document.getElementById("'+videoId+'").paused) '+
						'{if(riprodotto'+videoId+'<'+numberOfPlays+'){'+
							'document.getElementById("'+videoId+'").play();}'+
					'};'//'else document.getElementById("'+videoId+'").pause();'+
					+'if(riprodotto'+videoId+'>='+numberOfPlays+') '+
						'window.alert("Hai raggiunto il massimo numero di riproduzioni del video");}</script>'));
			div.append( CKEDITOR.dom.element.createFromHtml('<script>function '+videoId+'_ingrandisci(){'+videoId+'.width=700;}') );
			div.append( CKEDITOR.dom.element.createFromHtml('<script>function '+videoId+'_normale(){'+videoId+'.width=420;}') );					
		},
		onHide : function()
		{
			if ( this.previewImage )
			{
				this.previewImage.removeListener( 'load', onImgLoadEvent );
				this.previewImage.removeListener( 'error', onImgLoadErrorEvent );
				this.previewImage.removeListener( 'abort', onImgLoadErrorEvent );
				this.previewImage.remove();
				this.previewImage = null;		// Dialog is closed.
			}
		},

		contents :
		[
			{
				id : 'info',
				elements :
				[
					{
						type : 'hbox',
						widths: [ '', '100px'],
						children : [
							{
								type : 'text',
								id : 'poster',
								label : lang.poster,
								commit : commitValue,
								setup : loadValue,
								'default' : '/public/fileman/Uploads/Images1/UNIPG.png',
								onChange : function()
								{
									var dialog = this.getDialog(),
										newUrl = this.getValue();

									//Update preview image
									if ( newUrl.length > 0 )	//Prevent from load before onShow
									{
										dialog = this.getDialog();
										var preview = dialog.previewImage;

										preview.on( 'load', onImgLoadEvent, dialog );
										preview.on( 'error', onImgLoadErrorEvent, dialog );
										preview.on( 'abort', onImgLoadErrorEvent, dialog );
										preview.setAttribute( 'src', newUrl );
									}
								}
							},
							{
								type : 'button',
								id : 'browse',
								hidden : 'true',
								style : 'display:inline-block;margin-top:10px;',
								filebrowser :
								{
									action : 'Browse',
									target: 'info:poster',
									url: editor.config.filebrowserImageBrowseUrl || editor.config.filebrowserBrowseUrl
								},
								label : editor.lang.common.browseServer
							}]
					},
					{
						type : 'hbox',
						widths: [ '33%', '33%', '33%'],
						children : [
							{
								type : 'text',
								id : 'width',
								label : editor.lang.common.width,
								'default' : 400,
								validate : CKEDITOR.dialog.validate.notEmpty( lang.widthRequired ),
								commit : commitValue,
								setup : loadValue
							},
							{
								type : 'text',
								id : 'height',
								label : editor.lang.common.height,
								'default' : 300,
								validate : CKEDITOR.dialog.validate.notEmpty(lang.heightRequired ),
								commit : commitValue,
								setup : loadValue
							},
							{
								type : 'text',
								id : 'id',
								label : 'Id',
								setup : loadValue
							}
								]
					},
					{
						type : 'hbox',
						widths: [ '', '100px', '75px'],
						children : [
							{
								type : 'text',
								id : 'src0',
								label : lang.sourceVideo,
								commit : commitSrc,
								setup : loadSrc
							},
							{
								type : 'button',
								id : 'browse',
								hidden : 'true',
								style : 'display:inline-block;margin-top:10px;',
								filebrowser :
								{
									action : 'Browse',
									target: 'info:src0',
									url: editor.config.filebrowserVideoBrowseUrl || editor.config.filebrowserBrowseUrl
								},
								label : editor.lang.common.browseServer
							},
							{
								id : 'type0',
								label : lang.sourceType,
								type : 'select',
								'default' : 'video/mp4',
								items :
								[
									[ 'MP4', 'video/mp4' ],
									[ 'WebM', 'video/webm' ]
								],
								commit : commitSrc,
								setup : loadSrc
							}]
					},
					{
						type : 'hbox',
						widths: [ '', '100px', '75px'],
						children : [
							{
								type : 'text',
								id : 'numberOfPlays',
								label : lang.numberOfPlay,
								'default' : '2',
								validate : CKEDITOR.dialog.validate.notEmpty( lang.widthRequired ),
								commit : commitValue
							}
						]
					}
				]
			}

		]
	};
} );
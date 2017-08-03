CKEDITOR.dialog.add( 'video', function ( editor )
{
	var lang = editor.lang.video;
	var videoId;
	var numberOfPlays=2;
	var user_height=300;
	var user_width=400;
	var user_widthBigger=800;
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
				user_width=value;
				user_widthBigger=2*value;
				extraStyles.width = value + 'px';
				break;
			case 'height':
				videoNode.setAttribute( this.id, value);
				user_height=value;
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
		title : ttDialogTitleVideo,
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
				videoNode = CKEDITOR.dom.element.createFromHtml( '<cke:video id='+videoId+' preload="auto"></cke:video>', editor.document );
				
			}
			else
			{
				videoNode = this.videoNode;
			}

			var extraStyles = {}, videos = [];
			this.commitContent( videoNode, extraStyles, videos );

			var innerHtml = '', links = '',
				link = '<a href="%src%">%type%</a> ' || '',
				fallbackTemplate = ttFallbackTemplateVideo || '';
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
			div.append( CKEDITOR.dom.element.createFromHtml(''+
				'<style>'+
				'#'+videoId+'{pointer-events: none;}'+
				' .'+videoId+'_button{'+
      				' background-color: #4CAF50;'+
      				' border: none;'+
      				' color: white;'+
      				' padding: 15px;'+
    				' text-align: center;'+
    				' text-decoration: none;'+
    				' display: inline-block;'+
    				' font-size: 16px;'+
	     			' cursor: pointer;'+
	     			' border-radius: 8px;'+
	     			' box-shadow: 0 5px #666;'+
	     			' outline: none;}'+
	     		' .'+videoId+'_button:hover { '+
	    			' background-color: red;'+
	    			' color: white;'+
	    			' box-shadow: 0 5px #666;}'+
	    		' .'+videoId+'_button:active {'+
	    				' background-color: red;'+
	    				' box-shadow: 0 1px #666;'+
	    				' transform: translateY(4px);}'+
				'</style>') );

			div.append( CKEDITOR.dom.element.createFromHtml(''+
				'<div>'+
					' <button class="'+videoId+'_button" onclick="'+videoId+'_playPause()"><img id="playbutton_'+videoId+'" src="/ckeditor/plugins/video/dialogs/play.png" ></button>&nbsp; &nbsp; &nbsp;&nbsp;'+
					' <button class="'+videoId+'_button" onclick="'+videoId+'_bigger()"><img src="/ckeditor/plugins/video/dialogs/zoom.png" ></button>&nbsp; &nbsp; &nbsp;&nbsp;'+
					' <button class="'+videoId+'_button" onclick="'+videoId+'_standard()"><img src="/ckeditor/plugins/video/dialogs/resize.png" ></button>'+
				'</div>') );
			div.append( CKEDITOR.dom.element.createFromHtml(''+
				'<script>'+
					'var playbutton_'+videoId+'=document.querySelector("#playbutton_'+videoId+'");'+
					'var reproduced'+videoId+'=0;'+
					'var can_play'+videoId+'=0;'+
					'document.getElementById("'+videoId+'").addEventListener("ended",playbackcontrolcompleted_'+videoId+',false);'+
					'function playbackcontrolcompleted_'+videoId+'(e){'+
						'reproduced'+videoId+'=reproduced'+videoId+'+1;'+
						'if(reproduced'+videoId+'=='+numberOfPlays+'){'+
							'playbutton_'+videoId+'.setAttribute("src","/ckeditor/plugins/video/dialogs/cant_play.png");'+
						'} '+
				'}</script>') );
			div.append( CKEDITOR.dom.element.createFromHtml(''+
				'<script>'+
				'function '+videoId+'_playPause(){'+
					'if (document.getElementById("'+videoId+'").paused){ '+
						'if(reproduced'+videoId+'<'+numberOfPlays+' && can_play'+videoId+'==1)'+
							'document.getElementById("'+videoId+'").play();'+
					'};'+
				'}'+
				'</script>'));

			div.append( CKEDITOR.dom.element.createFromHtml(''+
				'<script>'+
					'function '+videoId+'_bigger(){'+
						''+videoId+'.width='+user_widthBigger+';}'+
				'</script>') );

			div.append( CKEDITOR.dom.element.createFromHtml(
				'<script>'+
					'function '+videoId+'_standard(){'+
						''+videoId+'.width='+user_width+';}'+
				'</script>') );	

			div.append( CKEDITOR.dom.element.createFromHtml(''+
				'<script>'+
					'document.getElementById("'+videoId+'").addEventListener("load",checkLoad_'+videoId+',false);'+
					'function checkLoad_'+videoId+'() {'+
						'if (document.getElementById("'+videoId+'").readyState === 4){'+
							'document.getElementById("'+videoId+'").setAttribute("poster","");'+
							'can_play'+videoId+'=1;'+
						'}else setTimeout(checkLoad_'+videoId+', 1000);'+
					'}'+
					'checkLoad_'+videoId+'();'+
				'</script>'
				/* 
				USE THE SCRIPT BELOW FOR DOWNLOAD ALL THE VIDEOS BEFORE PLAYING
				WARNING: HIGH CPU USAGE, HIGH NETWORK USAGE
				'<script>'+
					'var r_'+videoId+' = new XMLHttpRequest();'+
					'r_'+videoId+'.onload = function() {'+
					 	'document.getElementById("'+videoId+'").src=URL.createObjectURL(r_'+videoId+'.response);'+
						'document.getElementById("'+videoId+'").setAttribute("poster","");'+
						'can_play'+videoId+'=1;'+
					'};'+
					'r_'+videoId+'.open("GET","'+video.src+'");'+
					'r_'+videoId+'.responseType = "blob";'+
					'r_'+videoId+'.send();'+
				'</script>'	
				*/
				) 
			);
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
								label : ttPosterImageVideo,
								commit : commitValue,
								setup : loadValue,
								'default' : '/ckeditor/plugins/video/dialogs/camera.png',
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
								label : ttBrowseVideo
							}]
					},
					{
						type : 'hbox',
						widths: [ '33%', '33%', '33%'],
						children : [
							{
								type : 'text',
								id : 'width',
								label : ttWidthVideo,
								'default' : 400,
								validate : function() {
									if ( !this.getValue() || isNaN(this.getValue()) ){
										alert(ttWidthRequiredVideo);
										return false;
									}if(this.getValue()<100){
										alert(ttWidthRequiredVideo);
										return false
									}return true;
								},
								commit : commitValue,
								setup : loadValue
							},
							{
								type : 'text',
								id : 'height',
								label : ttHeightVideo,
								'default' : 300,
								validate : function() {
									if ( !this.getValue() || isNaN(this.getValue()) ){
										alert(ttHeightRequiredVideo);
										return false;
									}if(this.getValue()<100){
										alert(ttHeightRequiredVideo);
										return false
									}return true;
								},
								commit : commitValue,
								setup : loadValue
							},
							{
								type : 'text',
								id : 'id',
								label : ttIdVideo,
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
								label : ttsourceVideo,
								commit : commitSrc,
								validate : CKEDITOR.dialog.validate.notEmpty( ttEEmptyFields ),
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
								label : ttBrowseVideo
							},
							{
								id : 'type0',
								label : ttsourceVideo,
								type : 'select',
								'default' : 'video/mp4',
								items :
								[
									[ 'MP4', 'video/mp4' ]
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
								label : ttNumberOfPlaysVideo,
								'default' : '2',
								validate : function() {
									if ( !this.getValue() || isNaN(this.getValue()) ){
										alert(ttNumberOfPlayRequiredVideo);
										return false;
									}if(this.getValue()<1){
										alert(ttNumberOfPlayRequiredVideo);
										return false
									}return true;
								},
								commit : commitValue
							}
						]
					}
				]
			}

		]
	};
} );

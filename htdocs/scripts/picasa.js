/*
	CREATED BY: 	Paul Everton
	DATE:			10/27/2010
	DESCRIPTION:	Customizable PICASA JQuery Code. This can also be used as an example
					of how to grab PICASA web albums
	NOTES :

	JSON Reference: http://code.google.com/apis/picasaweb/docs/2.0/developers_guide_protocol.html
	This URL shows the RSS return feed. It will list the RSS for Albums and Photos. From this you can
	grab more information for the me_showAlbums and me_showPhotos functions.

	This version now contains basic fancybox support for the images. It has been seperated out into
	it's own function called me_fancyPhotos.


	BANNER INSTRUCTIONS:
	Note that the banner album will not display within the album list. A user can access the banner album
	if they know the complete URL (ie the album id for the banner album). Once a banner album has been created
	in Picasa, you must add the page location into the comment to get it to load on the page.
		EXAMPLE:
			http://www.somesite.com/en/folder/page.html  needs the banner.jpg picture, add /en/folder/page.html
			to the picture comment in Picasa.
	Also note that banners might not appear right away since the AJAX calls are GETs and not POSTS. This was done
	to speed up the query as GETs get cached by the browser.


	Default HTML structures:
	Albums:
	<ul id='album_location'>
		<li><a href='/directory/page.html?albumID=Number'>album title</a></li>
		<li><a href='/directory/page.html?albumID=Number'>album title</a></li>
		<li><a href='/directory/page.html?albumID=Number'>album title</a></li>
	</ul>

	Photos:
	<ul id='photo_location'>
		<li>
			<a class='fancy_link' href='url_photo_location'>
				<img src='url_photo_thumbnail_location'/>
			</a>
			<div class='caption'><p>photo caption</p></div>
		</li>
		<li>
			<a class='fancy_link' href='url_photo_location'>
				<img src='url_photo_thumbnail_location'/>
			</a>
			<div class='caption'><p>photo caption</p></div>
		</li>
		<li>
			<a class='fancy_link' href='url_photo_location'>
				<img src='url_photo_thumbnail_location'/>
			</a>
			<div class='caption'><p>photo caption</p></div>
		</li>
	</ul>

	Banner
	<any element id='banner_loc'><img src='url_photo_location' /></any element>
*/






/* 	******************************

		EDITABLE PARAMETERS

	****************************** */

/* ***************
	BASE PARAMETERS
****************** */

//the PICASA user ID
var gs_userID = 'jersey.edhardydeals';
//var gs_userID = 'angelagracekaplan';


/* The ID of the element where the album list should be placed.
Note that this ID should be a UL as the albums are placed in LI */
var gs_albumsIDLocation = 'album_loc';


/* The ID of the element where the photo list should be placed.
Note that this ID should be a UL as the photos are placed in LI */
var gs_picturesIDLocation = 'photo_loc';

/* The ID of the element where the album name for the current album should be placed.
this will just place text into the element as text */
var gs_albumNameIDLocation = 'album_name_loc';

/* the ID name given to the album link of the current album.
This class is given to the <li> element */
var gs_selectedAlbumID = 'selected-album';

//the album's href link
var gs_picturesHREFAbsolute = '/photoAlbum.php';


/* ***************
	EXTRA BASE PARAMETERS
****************** */

//if the album list should display with the photos
var gb_displayBoth = false;

//default album id to display on load. Leave as '' if you wish to have no default album
var gs_albumID = '';


/* ***************
	BANNER PARAMETERS
****************** */

/* The ID of the element where the banner should be placed.*/
var gs_bannerIDLocation = '';

//the album id for the banners. This album will not be displayed in the list
var gs_bannersAlbumID = '';

//the default page. this is to handle the base index page that is not display
//Example. index.html at a website can be www.somesite.com/ or www.somesite.com/index.html
var gs_bannerDefaultPage = '';


/* ***************
	VIDEO PLAYER PARAMETERS
****************** */

//the location of the flowplayer. This will probable not need to change
var gs_videoPlayerLoc = "/includes/flowplayer/flowplayer-3.2.7.swf";



//loading function
$(document).ready(function($) {
	me_loadPicasa();
});



/*
FUNTION NAME	: me_loadPicasa
PARAMETERS		: N/A
RETURN			: N/A
DESCRIPTION		: This function will load the picasa albums
*/
function me_loadPicasa() {
	//variable definition
	var ls_URLAlbumID = '';

	//get the URL album id
	ls_URLAlbumID = gup('albumID');


	//if the url does not have an album ID, use the default one
	if( ls_URLAlbumID!= ''){
		gs_albumID = ls_URLAlbumID;
	}

	//trim out white space
	gs_albumID = trimString(gs_albumID);


	//check to see if albums should be displayed
	if(gb_displayBoth || (gs_albumID == '')){
		//ajax call to load the albums
		$.ajax({ url: 'http://picasaweb.google.com/data/feed/api/user/' + gs_userID + '?alt=json-in-script',
		success: me_showAlbums,
		error: function(){
			alert('There was an error loading the albums. Please reload this page.');
		},
		dataType: 'jsonp'
		});
	}


	//check to see if the album pictures should load
	if(gs_albumID != ''){
		//pull the album
		$.ajax({ url: 'http://picasaweb.google.com/data/feed/api/user/' + gs_userID + '/albumid/' + gs_albumID + '?alt=json-in-script',
		success: me_showPhotos,
		error: function(){
			alert('There was an error loading the photos. Please reload this page.');
		},
		dataType: 'jsonp'
		});
	}


	//check to see if the banner picture should load
	if(trimString(gs_bannersAlbumID) != ''){
		//pull the album
		$.ajax({ url: 'http://picasaweb.google.com/data/feed/api/user/' + gs_userID + '/albumid/' + gs_bannersAlbumID + '?alt=json-in-script',
		success: me_showBanner,
		error: function(){
			alert('There was an error loading the photos. Please reload this page.');
		},
		dataType: 'jsonp'
		});
	}


}



/*
FUNTION NAME	: me_showAlbums
PARAMETERS		: JSON pjson_data, p_status?
RETURN			: N/A
DESCRIPTION		: This function will load the picasa albums
*/
function me_showAlbums(pjson_data, p_status) {
	//variable declaration
    var ljson_albums = pjson_data.feed.entry;


	//loop over each album
    $.each(ljson_albums, function() {
		/*
			ALBUM DATA:
			This is the data returned on each album. Use the variables
			to construct your HTML
		*/

		/*
			album ID number. This should be added on as a
			URL parameter so the code know which album to grab photos.
			EXAMPLE: /somepage.html?albumID=" + ls_albumID + "
		*/
		var ls_albumID = this.gphoto$id.$t;

		//album title
		var ls_albumTitle = this.title.$t;

		//album alternate
		var ls_albumAlternate = this.link[1].href;

		//album summary with adding BR for line feeds
		var ls_albumSummary = this.summary.$t;
		ls_albumSummary = ls_albumSummary.replace(String.fromCharCode(10), "<br /><br />");
		ls_albumSummary = ls_albumSummary.replace(String.fromCharCode(13), "<br /><br />");

		/*
			album thumbnail location
			EXAMPLE: somePlaceAtGoogle/photoThumb.jpg
		*/
		var ls_albumThumbNail = this.media$group.media$thumbnail[0].url;

		/*
			album photo location
			EXAMPLE: somePlaceAtGoogle/photo.jpg
		*/
		var ls_albumImage = this.media$group.media$content[0].url;


		/*
			The local ID for the selected album
		*/
		var ls_selectedAlbumID = '';

		//if it's the current active album, set the ID to the LI element
		if(gs_albumID == ls_albumID){
			ls_selectedAlbumID = "id='" + gs_selectedAlbumID + "'";
		}


		/*
			if the banner album has been defined, do not list it in the album list
			The logic to not display an album is as follows
			The Banner Album ID is defined
			AND
			(
			 	Banner Album ID matches current Album ID
			 	OR
				Banner Album ID is the Alternate
			 )
		*/

       if(!((trimString(gs_bannersAlbumID) != '') && ((gs_bannersAlbumID == ls_albumID) || (ls_albumAlternate == ('http://picasaweb.google.com/' + gs_userID + '/' + gs_bannersAlbumID))))){
			 // Create album element
			$("<li " + ls_selectedAlbumID + "><a href='" + gs_picturesHREFAbsolute + "?albumID=" + ls_albumID + "'><img class='album-thumb' src='" + ls_albumThumbNail + "' /></a><div class='album-caption'>" + ls_albumTitle + "<br />" + ls_albumSummary + "</div></li>")
			.appendTo("#" + gs_albumsIDLocation);

		}
    });
}





/*
FUNTION NAME	: me_showPhotos
PARAMETERS		: JSON pjson_data, p_status?
RETURN			: N/A
DESCRIPTION		: This function will load the picasa albums
*/
function me_showPhotos(pjson_data, p_status) {
	//variable declaration
	var ljson_photos = pjson_data.feed.entry;

	//current album title
	var ls_currentAlbumTitle = pjson_data.feed.title.$t;

	//set the album name
	$('<span id="me_album_title">' + ls_currentAlbumTitle + '</span>').appendTo("#" + gs_albumNameIDLocation);


	//loop over photos
    $.each(ljson_photos, function() {
		/*
			PHOTO DATA:
			This is the data returned on each photo. Use the variables
			to construct your HTML
		*/

		/*
			the original photo file location
			EXAMPLE: somePlaceAtGoogle/photo.jpg
		*/
		var ls_photoOriginal = this.media$group.media$content[0].url;


		/*
			video data.
			ls_videoSmall = flv video at 320x240
			ls_videoLarge = mp4 at 480x360
		*/
		var ls_videoSmall = '';
		var ls_videoLarge = '';
		var lb_videoFound = false;

		if(this.media$group.media$content.length > 1){
			lb_videoFound = true;
			ls_videoSmall = this.media$group.media$content[1].url;
			ls_videoLarge = this.media$group.media$content[2].url;
		}

		/*
			the photo thumbnail location
			EXAMPLE: somePlaceAtGoogle/photoThumb.jpg
		*/
		var ls_photoThumbnail = this.media$group.media$thumbnail[0].url;

		//photo description with adding br on line feed
		var ls_photoDescription = this.media$group.media$description.$t;
		ls_photoDescription = ls_photoDescription.replace(String.fromCharCode(10), "<br /><br />");
		ls_photoDescription = ls_photoDescription.replace(String.fromCharCode(13), "<br /><br />");

		//photo title with adding br on line feed
		var ls_photoTitle = this.media$group.media$title.$t;
		ls_photoTitle = ls_photoTitle.replace(String.fromCharCode(10), "<br /><br />");
		ls_photoTitle = ls_photoTitle.replace(String.fromCharCode(13), "<br /><br />");

		//photo caption with adding br on line feed
		var ls_photoCaption = (this.summary.$t ? this.summary.$t : "");
		ls_photoCaption = ls_photoCaption.replace(String.fromCharCode(10), "<br /><br />");
		ls_photoCaption = ls_photoCaption.replace(String.fromCharCode(13), "<br /><br />");


		/*
			These are the default values for the constructed element.
			ls_aClassValue is used to define the <a> tag class for fancy box.
			ls_hrefValue is for the actual file link. These will change if a
			video is found.
		*/
		var ls_aClassValue = 'fancy_link';
		var ls_hrefValue = ls_photoOriginal;

		if(lb_videoFound){
			//video link
			ls_aClassValue = 'fancy_link_video';
			ls_hrefValue = ls_videoSmall;
		}


		//create the element
		$("<li><a rel='group' class='" + ls_aClassValue + "' href='" + ls_hrefValue + "'><img src='" + ls_photoThumbnail + "'/></a><div class='caption'><p>" + ls_photoCaption + "</p></div></li>").appendTo("#" + gs_picturesIDLocation);



    });

	//fancybox the images
	me_fancyPhotos();

}



/*
FUNTION NAME	: me_fancyPhotos
PARAMETERS		: N/A
RETURN			: N/A
DESCRIPTION		: This function fancy the photo thumbs
*/
function me_fancyPhotos() {
	//variable declaration

	//fancybox the photos
	$(".fancy_link").fancybox();


	//fancybox the videos
	$(".fancy_link_video").fancybox({
		'content'			: '<div id="video_loc" style="height: 265px; width: 365px;"></div>',
		'hideOnContentClick':false,
		'overlayOpacity' :.6,
		'zoomSpeedIn'    :400,
		'zoomSpeedOut'   :400,
		'easingIn'		 : 'easeOutBack',
		'easingOut'		 : 'easeInBack',
		'onComplete' 	 :	function(){
				var player = flowplayer("video_loc", gs_videoPlayerLoc, {
				clip: {
					url: escape(this.href),
					type: 'flv',
					autoPlay:true,
					autoBuffering:true
				}
			});
			player.load();

		}
	});
}


/*
FUNTION NAME	: me_showBanner
PARAMETERS		: JSON pjson_data, p_status?
RETURN			: N/A
DESCRIPTION		: This function will load the appropriate banner

	Note that the banner album will not display within the album list. A user can access the banner album
	if they know the complete URL (ie the album id for the banner album). Once a banner album has been created
	in Picasa, you must add the page location into the comment to get it to load on the page.
		EXAMPLE:
			http://www.somesite.com/en/folder/page.html  needs the banner.jpg picture, add /en/folder/page.html
			to the picture comment in Picasa.
	Also note that banners might not appear right away since the AJAX calls are GETs and not POSTS. This was done
	to speed up the query as GETs get cached by the browser.


*/
function me_showBanner(pjson_data, p_status) {
	//variable declaration
	var ljson_photos = pjson_data.feed.entry;
	var ls_scriptName = window.location.pathname;

	//this is to handle default page situations like
	//index.html can be www.somesite.com/ or www.somesite.com/index.html
	if(trimString(ls_scriptName) == '/'){
		ls_scriptName = gs_bannerDefaultPage;
	}


	//loop over photos
    $.each(ljson_photos, function() {
		/*
			PHOTO DATA:
			This is the data returned on each photo. Use the variables
			to construct your HTML
		*/

		/*
			the original photo file location
			EXAMPLE: somePlaceAtGoogle/photo.jpg
		*/
		var ls_photoOriginal = this.media$group.media$content[0].url;

		/*
			the photo thumbnail location
			EXAMPLE: somePlaceAtGoogle/photoThumb.jpg
		*/
		var ls_photoThumbnail = this.media$group.media$thumbnail[0].url;

		//photo description with adding br on line feed
		var ls_photoDescription = this.media$group.media$description.$t;

		ls_photoDescription = ls_photoDescription.replace(String.fromCharCode(10), "<br /><br />");
		ls_photoDescription = ls_photoDescription.replace(String.fromCharCode(13), "<br /><br />");

		//photo title with adding br on line feed
		var ls_photoTitle = this.media$group.media$title.$t;
		ls_photoTitle = ls_photoTitle.replace(String.fromCharCode(10), "<br /><br />");
		ls_photoTitle = ls_photoTitle.replace(String.fromCharCode(13), "<br /><br />");

		//photo caption with adding br on line feed
		var ls_photoCaption = (this.summary.$t ? this.summary.$t : "");
		ls_photoCaption = ls_photoCaption.replace(String.fromCharCode(10), "<br /><br />");
		ls_photoCaption = ls_photoCaption.replace(String.fromCharCode(13), "<br /><br />");

		//add in the banner if it's the appropriate page
		if(trimString(ls_scriptName) == trimString(ls_photoDescription)){

			//clear out the banner location
			$('#' + gs_bannerIDLocation).empty();

			//create the element
			$("<img src='" + ls_photoOriginal + "'/>").appendTo("#" + gs_bannerIDLocation);
		}

    });
}







/*
	****************************

		EXTRA NEEDED FUNCTIONS


	*****************************
*/

// grabs URL Parameters
function gup( name ){  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");  var regexS = "[\\?&]"+name+"=([^&#]*)";  var regex = new RegExp( regexS );  var results = regex.exec( window.location.href );  if( results == null )    return "";  else    return results[1];}

//Trim Function ULTRA FAST!
function trimString(str) {
	var	str = str.replace(/^\s\s*/, ''),
		ws = /\s/,
		i = str.length;
	while (ws.test(str.charAt(--i)));
	return str.slice(0, i + 1);
}





<?php

class FLV extends File 
{
	public static $allowed_file_types = array(
		'flv','avi','mov','mpeg','mpg'
	);

	private $allow_full_screen = true;	

	public static $player_count = 0;
	public static $video_width = 400;
	public static $video_height = 300;
	public static $default_thumbnail_width = 400;
	public static $default_thumbnail_height = 300;
	public static $default_popup_width = 800;
	public static $default_popup_height = 450;

	public static $thumbnail_seconds = 1;
	public static $audio_sampling_rate = 22050;
	public static $audio_bit_rate = 32;
	
	public static function echo_ffmpeg_test()
	{
		if(extension_loaded('ffmpeg'))
			$success = true;
		else if(strlen(exec("ffmpeg")))
			$success = true;
		else
			$success = false;
		
		echo $success ? "<span style='color:green'>FFMPEG is installed on your server and working properly.</span>" : "<span class='color:red'>FFMPEG does not appear to be installed on your server.</span>";
	}
	
	
	private function SWFLink()
	{
		return Director::absoluteURL('dataobject_manager/code/flv/shadowbox/libraries/mediaplayer/player.swf');
	}
	
	private function AllowFullScreen()
	{
		return $this->allow_full_screen ? "true" : "false";
	}
	
	private static function remove_file_extension($filename)
	{
		$ext = strrchr($filename, '.');  
		if($ext !== false)  
			$filename = substr($filename, 0, -strlen($ext));  
		return $filename;
	}
	
	public function Icon()
	{
		return "sapphire/images/app_icons/mov_32.gif";
	}
	
	public function FLVPath()
	{
		return self::remove_file_extension($this->Filename).".flv";		
	}
	
	public function FLVLink()
	{
		return Director::absoluteURL($this->FLVPath());
	}
	
	private function absoluteRawVideoLink()
	{
		return Director::baseFolder()."/".$this->Filename;	
	}
	
	private function absoluteFLVPath()
	{
		return Director::baseFolder()."/".$this->FLVPath();
	}
	
	private function hasFLV()
	{
		return Director::fileExists(self::remove_file_extension($this->Filename).".flv");
	}
	
	private function createFLV()
	{
/*die(sprintf("ffmpeg -i %s -ar %d -ab %d -f flv %s",
			$this->absoluteRawVideoLink(),
			self::$audio_sampling_rate,
			self::$audio_bit_rate,
			$this->absoluteFLVLink()
		));
*/		
		
		exec(sprintf("ffmpeg -i %s -ar %d -ab %d -f flv %s",
			$this->absoluteRawVideoLink(),
			self::$audio_sampling_rate,
			self::$audio_bit_rate,
			$this->absoluteFLVPath()
		));	
	}
	
	public function onBeforeWrite()
	{
		parent::onBeforeWrite();
		if(!$this->hasFLV())
			$this->createFLV();
	}
	
	
	public function Player($width = null, $height = null)
	{
		if($width === null) $width = self::$video_width;
		if($height === null) $height = self::$video_height;
		
		self::$player_count++;
		Requirements::javascript('dataobject_manager/code/flv/swfobject.js');
		Requirements::customScript(sprintf(
				"swfobject.embedSWF('%s','player-%s','%d','%d','9.0.0','expressInstall.swf',{file : '%s'},{allowscriptaccess : 'true', allowfullscreen : '%s'})",
				$this->SWFLink(),
				self::$player_count,
				$width,
				$height,
				$this->FLVLink(),
				$this->AllowFullScreen()
			)
		);
		return "<div id='player-".self::$player_count."'>Loading...</div>";
	}
	
	public function forTemplate()
	{
		return $this->Player();
	}
	
	public function VideoThumbnail($width = null, $height = null)
	{
		if($width === null) $width = self::$default_thumbnail_width;
		if($height === null) $height = self::$default_thumbnail_height;
		
		$thumb = self::remove_file_extension($this->Filename)."_thumb_{$width}_{$height}.jpg";
		$abs_thumb = Director::baseFolder()."/".$thumb;
		if(!Director::fileExists($thumb)) {
			exec(sprintf("ffmpeg -y -i %s -f mjpeg -ss %d -s %s -an %s",
				$this->absoluteRawVideoLink(),
				self::$thumbnail_seconds,
				$width."x".$height,
				$abs_thumb
			));
		}

		return sprintf("<img src='%s' alt='%s' width='%d' height='%d' />",
			Director::absoluteURL($thumb),
			$this->Title,
			$width,
			$height
		);
	}
	
	public function VideoPopup($thumb_width = null, $thumb_height = null, $popup_width = null, $popup_height = null)
	{
		if($popup_width === null) $popup_width = self::$default_popup_width;
		if($popup_height === null) $popup_height = self::$default_popup_height;
		
		return $this->customise(array(
			'PopupWidth' => $popup_width,
			'PopupHeight' => $popup_height,
			'Title' => $this->Title,
			'Link' => $this->FLVLink(),
			'Thumbnail' => $this->VideoThumbnail($thumb_width, $thumb_height)
		))->renderWith(array('FLVpopup'));
		
	}
	
	

	
}


?>
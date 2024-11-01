<?php
/**
* PHP Exif Tool Liberary
*
*/
require __DIR__ . '/autoload.php';
use Monolog\Logger;
use PHPExiftool\Reader;
use PHPExiftool\Driver\Value\ValueInterface;
use PHPExiftool\Writer;
use PHPExiftool\Driver\Metadata\Metadata;
use PHPExiftool\Driver\Metadata\MetadataBag;
use PHPExiftool\Driver\Tag\IPTC\ObjectName;
use PHPExiftool\Driver\Value\Mono;
use PHPExiftool\Driver\Tag\XMPPhotoshop\City;
use PHPExiftool\Driver\Tag\XMPXmp\Keywords;
use PHPExiftool\Driver\Tag\IPTC\Credit;
use PHPExiftool\Driver\Tag\XMPPlus\LicensorURL;
use PHPExiftool\Driver\Tag\XMPPlus\ImageCreatorName;
use PHPExiftool\Driver\Tag\XMPPlus\ImageCreatorID;
use PHPExiftool\Driver\Tag\XMPPlus\ImageCreatorImageID;
use PHPExiftool\Driver\Tag\XMPPhotoshop\Credit as XMPCredit;
use PHPExiftool\Driver\Tag\XMPDc\Creator;
use PHPExiftool\Driver\Tag\IPTC\ByLine;
use PHPExiftool\Driver\Tag\IPTC\ByLineTitle;
use PHPExiftool\Driver\Tag\XMPXmpRights\WebStatement;
use PHPExiftool\Driver\Tag\XMPDc\Title;
use PHPExiftool\Driver\Tag\IPTC\Headline as IPTC_Headline;
use PHPExiftool\Driver\Tag\XMPDc\Description;
use PHPExiftool\Driver\Tag\IPTC\CaptionAbstract;
use PHPExiftool\Driver\Tag\XMPDc\Rights;
use PHPExiftool\Driver\Tag\IPTC\CopyrightNotice;
use PHPExiftool\Driver\Tag\XMPPhotoshop\Headline;
use PHPExiftool\Driver\Tag\XMPDc\Subject;
use PHPExiftool\Driver\Tag\IPTC\Keywords as IPTC_Keywords;
use PHPExiftool\Driver\Tag\XMPPlus\CopyrightOwnerName;
use PHPExiftool\Driver\Tag\XMPPlus\CopyrightOwnerImageID;
use PHPExiftool\Driver\Tag\XMPPlus\CopyrightOwnerID;
use PHPExiftool\Driver\Tag\XMPPlus\CopyrightOwner;
use PHPExiftool\Driver\Tag\XMPPlus\CopyrightStatus;
use PHPExiftool\Driver\Tag\XMPPlus\PLUSVersion;
use PHPExiftool\Driver\Tag\XMPIptcExt\RegistryID;
use PHPExiftool\Driver\Tag\XMPIptcExt\RegistryItemID;
use PHPExiftool\Driver\Tag\XMPIptcExt\RegistryEntryRole;
use PHPExiftool\Driver\Tag\XMPIptcExt\RegistryOrganisationID;
use PHPExiftool\Driver\Tag\XMPPlus\LicensorName;
use PHPExiftool\Driver\Tag\XMPPlus\LicenseID;
use PHPExiftool\Driver\Tag\XMPPlus\LicensorEmail;
use PHPExiftool\Driver\Tag\XMPPlus\LicensorImageID;
use PHPExiftool\Driver\Tag\XMPPlus\TermsAndConditionsURL;
use PHPExiftool\Driver\Tag\XMPPlus\MediaConstraints;
use PHPExiftool\Driver\Tag\XMPPlus\MediaSummaryCode;
use PHPExiftool\Driver\Tag\XMPPlus\LicenseeID;
use PHPExiftool\Driver\Tag\XMPPlus\LicenseeTransactionID;
use PHPExiftool\Driver\Tag\XMPPlus\LicensorTransactionID;
use PHPExiftool\Driver\Tag\XMPPlus\LicenseTransactionDate;
use PHPExiftool\Driver\Tag\XMPPlus\LicenseStartDate;
use PHPExiftool\Driver\Tag\XMPPlus\LicenseeName;
use PHPExiftool\Driver\Tag\XMPPlus\EndUserName;
use PHPExiftool\Driver\Tag\XMPPlus\EndUserID;
use PHPExiftool\Driver\Tag\XMPPlus\LicenseeImageID;
use PHPExiftool\Driver\Tag\XMPPlus\FileNameAsDelivered;
use PHPExiftool\Driver\Tag\XMPPlus\ImageFileConstraints;
use PHPExiftool\Driver\Tag\XMPPlus\CreditLineRequired;
use PHPExiftool\Driver\Tag\XMPPlus\LicensorID;
use PHPExiftool\Driver\Tag\XMPPlus\OtherConditions;
use PHPExiftool\Driver\Tag\XMPPhotoshop\Source;
use PHPExiftool\Driver\Tag\IPTC\Source as IPTC_Source;
use PHPExiftool\Driver\Tag\XMPPhotoshop\CaptionWriter;
use PHPExiftool\Driver\Tag\IPTC\WriterEditor;
use PHPExiftool\Driver\Tag\IFD0\ModifyDate;
use PHPExiftool\Driver\Tag\ExifIFD\ExifVersion;
use PHPExiftool\Driver\Tag\ExifIFD\DateTimeOriginal;
use PHPExiftool\Driver\Tag\ExifIFD\CreateDate;
use PHPExiftool\Driver\Tag\ExifIFD\OffsetTimeOriginal;
use PHPExiftool\Driver\Tag\ExifIFD\OffsetTimeDigitized;
//use PHPExiftool\Driver\Tag\File\ImageWidth;
//use PHPExiftool\Driver\Tag\File\ImageHeight;
use PHPExiftool\Driver\Tag\ExifIFD\OffsetTime;
use PHPExiftool\Driver\Tag\ExifIFD\ComponentsConfiguration;
use PHPExiftool\Driver\Tag\ExifIFD\FlashpixVersion;
use PHPExiftool\Driver\Tag\ExifIFD\ColorSpace;
/**
 * CLink Exif Tool Metadata Writter
 * 
 * @param string image_path image path
 * @param array data metadata
 * @param integer attachment_if attachment id
 * 
 */
function wpclink_metadata_writter($image_path = false, $data = array(), $attachment_id = false, $scaled_only = false, $cache_id = 0){
	
	
	$logger = new Logger('exiftool');
	$writer = Writer::create($logger);
	$bag = new MetadataBag();
	$metadata = array();
	
	
	// Title
	if(isset($data['title'])){
		
		// XMP
		$bag->add(new Metadata(new Title(), new Mono($data['title'])));
		$metadata["XMP-dc:Title"] = $data['title'];
		
		// IPTC
		$bag->add(new Metadata(new ObjectName(), new Mono($data['title'])));
		$metadata["IPTC:ObjectName"] = $data['title'];
		
	}
	// Headline
	if(isset($data['image_title'])){
		// XMP
		$bag->add(new Metadata(new Headline(), new Mono($data['image_title'])));
		$metadata["XMP-photoshop:Headline"] = $data['image_title'];
		
		// IPTC
		$bag->add(new Metadata(new IPTC_Headline(), new Mono($data['image_title'])));
		$metadata["IPTC:Headline"] = $data['image_title'];
	}
	// Image Creator ID
	if(isset($data['image_creator_ID'])){
		
		// XMP
		$bag->add(new Metadata(new ImageCreatorID(), new Mono($data['image_creator_ID'])));
		$metadata["XMP-plus:ImageCreatorID"] = $data['image_creator_ID'];
		
		$bag->add(new Metadata(new CopyrightOwnerID(), new Mono($data['image_creator_ID'])));
		$metadata["XMP-plus:CopyrightOwnerID"] = $data['image_creator_ID'];
	}
	// Image Creator Name
	if(isset($data['image_creator_name'])){
		
		// XMP
		$bag->add(new Metadata(new ImageCreatorName(), new Mono($data['image_creator_name'])));
		$metadata["XMP-plus:ImageCreatorName"] = $data['image_creator_name'];
		
		$bag->add(new Metadata(new CopyrightOwnerName(), new Mono($data['image_creator_name'])));
		$metadata["XMP-plus:CopyrightOwnerName"] = $data['image_creator_name'];
	
	}
	// Creation ID
	if(isset($data['creation_ID'])){
		
		// XMP
		$bag->add(new Metadata(new ImageCreatorImageID(), new Mono($data['creation_ID'])));
		$metadata["XMP-plus:ImageCreatorImageID"] = $data['creation_ID'];
		
		$bag->add(new Metadata(new CopyrightOwnerImageID(), new Mono($data['creation_ID'])));
		$metadata["XMP-plus:CopyrightOwnerImageID"] = $data['creation_ID'];
		
		$bag->add(new Metadata(new CopyrightStatus(), new Mono("Protected")));
		$metadata["XMP-plus:CopyrightStatus"] = "CS-PRO";
		
		$bag->add(new Metadata(new PLUSVersion(), new Mono('1.2.2')));
		$metadata["XMP-plus:PLUSVersion"] = "1.2.2";
		
	}
	// Registery Item ID
	if(isset($data['registry_item_ID'])){
		
		$bag->add(new Metadata(new RegistryOrganisationID(), new Mono('https://clink.id')));
		$metadata["XMP-iptcExt:RegistryOrganisationID"] = "https://clink.id";
		
		$bag->add(new Metadata(new RegistryItemID(), new Mono($data['registry_item_ID'])));
		$metadata["XMP-iptcExt:RegistryItemID"] = $data['registry_item_ID'];
		
		$bag->add(new Metadata(new RegistryEntryRole(), new Mono('Handle')));
		$metadata["XMP-iptcExt:RegistryEntryRole"] = "Handle";
		
	}
	// Registery Item ID at 1
	if(isset($data['registry_item_IDat1'])){
		
		$bag->add(new Metadata(new RegistryOrganisationID(), new Mono('https://clink.id')));
		$metadata["XMP-iptcExt:RegistryOrganisationID"] = "https://clink.id";
		
		// Cordra
		$bag->add(new Metadata(new RegistryItemID(), new Mono($data['registry_item_IDat1'])));
		$metadata["XMP-iptcExt:RegistryItemID"] = $data['registry_item_IDat1'];
		
		$bag->add(new Metadata(new RegistryEntryRole(), new Mono('Handle')));
		$metadata["XMP-iptcExt:RegistryEntryRole"] = "Handle";
	}
	
	if(isset($data['registry_item_IDat3'])){
		$bag->add(new Metadata(new RegistryOrganisationID(), new Mono('https://clink.id')));
		$metadata["XMP-iptcExt:RegistryOrganisationID"] = "https://clink.id";
		
		// SHA256
		$bag->add(new Metadata(new RegistryItemID(), new Mono($data['registry_item_IDat3'])));
		$metadata["XMP-iptcExt:RegistryItemID"] = $data['registry_item_IDat3'];
		
		
		$bag->add(new Metadata(new RegistryEntryRole(), new Mono('SHA256Value')));
		$metadata["XMP-iptcExt:RegistryEntryRole"] = "SHA256Value";
		
	}
	
	if(isset($data['registry_item_IDat2'])){
		
		$bag->add(new Metadata(new RegistryOrganisationID(), new Mono('https://clink.id')));
		$metadata["XMP-iptcExt:RegistryOrganisationID"] = "https://clink.id";
		
		// ISCC
		$bag->add(new Metadata(new RegistryItemID(), new Mono($data['registry_item_IDat2'])));
		$metadata["XMP-iptcExt:RegistryItemID"] = $data['registry_item_IDat2'];
		
		
		$bag->add(new Metadata(new RegistryEntryRole(), new Mono('ISCC')));
		$metadata["XMP-iptcExt:RegistryEntryRole"] = "ISCC";
		
	}
	
	// Licensor Display Name
	if(isset($data['licensor_display_name'])){
		$bag->add(new Metadata(new LicensorName(), new Mono($data['licensor_display_name'])));
		$metadata["XMP-plus:LicensorName"] = $data['licensor_display_name'];
	}
	// Caption Writter
	if(isset($data['caption_writer'])){
		$bag->add(new Metadata(new CaptionWriter(), new Mono($data['caption_writer'])));
		$metadata["XMP-photoshop:CaptionWriter"] = $data['caption_writer'];
		
		$bag->add(new Metadata(new WriterEditor(), new Mono($data['caption_writer'])));
		$metadata["IPTC:Writer-Editor"] = $data['caption_writer'];
	}
	// Other Condition
	if(isset($data['linked_other_conditions'])){
		$bag->add(new Metadata(new OtherConditions(), new Mono(str_replace(",",", ",$data['linked_other_conditions']))));
		$metadata["XMP-plus:OtherConditions"] = str_replace(",",", ",$data['linked_other_conditions']);
	}
	// Licensor Email
	if(isset($data['licensor_email'])){
		$bag->add(new Metadata(new LicensorEmail(), new Mono('Email (Protected)')));
		$metadata["XMP-plus:LicensorEmail"] = "Email (Protected)";
	}
	// Licensor Image ID
	if(isset($data['licensor_image_ID'])){
		$bag->add(new Metadata(new LicensorImageID(), new Mono($data['licensor_image_ID'])));
		$metadata["XMP-plus:LicensorImageID"] = $data['licensor_image_ID'];
	}
	// Licensor ID
	if(isset($data['licensor_ID'])){
		$bag->add(new Metadata(new LicensorID(), new Mono($data['licensor_ID'])));
		$metadata["XMP-plus:LicensorID"] = $data['licensor_ID'];
	}
	// Terms and Condition URL
	if(isset($data['termsandcondition_url'])){
		$bag->add(new Metadata(new TermsAndConditionsURL(), new Mono($data['termsandcondition_url'])));
		$metadata["XMP-plus:TermsAndConditionsURL"] = $data['termsandcondition_url'];
	}
	// Media Constraints
	if(isset($data['mediaconstraints'])){
		$bag->add(new Metadata(new MediaConstraints(), new Mono($data['mediaconstraints'])));
		$metadata["XMP-plus:MediaConstraints"] = $data['mediaconstraints'];
	}
	// Image File Constraints
	if(isset($data['imagefileconstraints'])){
		$bag->add(new Metadata(new ImageFileConstraints(), new Mono("Maintain Metadata")));
		$metadata["XMP-plus:ImageFileConstraints"] = "IF-MMD";
	}
	// Credit Line Required
	if(isset($data['creditlinerequired'])){
		$bag->add(new Metadata(new CreditLineRequired(), new Mono("Credit on Image")));
		$metadata["XMP-plus:CreditLineRequired"] = "CR-COI";
	}
	// Image File Contraints
	if(isset($data['linked_ImageFileConstraints'])){
		$bag->add(new Metadata(new ImageFileConstraints(), new Mono($data['linked_ImageFileConstraints'])));
		$metadata["XMP-plus:ImageFileConstraints"] = $data['linked_ImageFileConstraints'];
		
		$bag->add(new Metadata(new ImageFileConstraints(), new Mono("Maintain File Name")));
		$metadata["XMP-plus:ImageFileConstraints"] = $metadata["XMP-plus:ImageFileConstraints"].' ; '."IF-MFN";
	}
	// Credit Line Required
	if(isset($data['linked_CreditLineRequired'])){
		$bag->add(new Metadata(new CreditLineRequired(), new Mono($data['linked_CreditLineRequired'])));
		$metadata["XMP-plus:CreditLineRequired"] = $data['linked_CreditLineRequired'];
	}
	// Copyright Status
	if(isset($data['linked_CopyrightStatus'])){
		$bag->add(new Metadata(new CopyrightStatus(), new Mono($data['linked_CopyrightStatus'])));
		$metadata["XMP-plus:CopyrightStatus"] = $data['linked_CopyrightStatus'];
	}
	// Media Summary Code
	if(isset($data['mediasummarycode'])){
		$bag->add(new Metadata(new MediaSummaryCode(), new Mono($data['mediasummarycode'])));
		$metadata["XMP-plus:MediaSummaryCode"] = "1IAC";		
	}
	// Licensee and Licensor IDs
	if(isset($data['right_transaction_ID'])){
		
		$bag->add(new Metadata(new LicenseID(), new Mono($data['right_transaction_ID'])));
		$metadata["XMP-plus:LicenseID"] = $data['right_transaction_ID'];
		
		$bag->add(new Metadata(new LicenseeTransactionID(), new Mono($data['right_transaction_ID'])));
		$metadata["XMP-plus:LicenseeTransactionID"] = $data['right_transaction_ID'];
		
		$bag->add(new Metadata(new LicensorTransactionID(), new Mono($data['right_transaction_ID'])));
		$metadata["XMP-plus:LicensorTransactionID"] = $data['right_transaction_ID'];
	}
	// License Transation Date and Start Time
	if(isset($data['right_transaction_time'])){
		
		$bag->add(new Metadata(new LicenseTransactionDate(), new Mono($data['right_transaction_time'])));
		$bag->add(new Metadata(new LicenseStartDate(), new Mono($data['right_transaction_time'])));
	}
	// Licensee Name
	if(isset($data['licensee_display_name'])){
		
		$bag->add(new Metadata(new LicenseeName(), new Mono($data['licensee_display_name'])));
		$metadata["XMP-plus:LicenseeName"] = $data['licensee_display_name'];
		
		$bag->add(new Metadata(new EndUserName(), new Mono($data['licensee_display_name'])));
		$metadata["XMP-plus:EndUserName"] = $data['licensee_display_name'];
	}
	// Licensee ID
	if(isset($data['licensee_ID'])){
		$bag->add(new Metadata(new LicenseeID(), new Mono($data['licensee_ID'])));
		$metadata["XMP-plus:LicenseeID"] = $data['licensee_ID'];
		
		$bag->add(new Metadata(new EndUserID(), new Mono($data['licensee_ID'])));
		$metadata["XMP-plus:EndUserID"] = $data['licensee_ID'];
	}
	// Licensee Image ID
	if(isset($data['linked_creation_ID'])){
		$bag->add(new Metadata(new LicenseeImageID(), new Mono($data['linked_creation_ID'])));
		$metadata["XMP-plus:LicenseeImageID"] = $data['linked_creation_ID'];
	}
	// Photoshop Source	
	if(isset($data['photoshop_source'])){
		$bag->add(new Metadata(new Source(), new Mono($data['photoshop_source'])));
		$metadata["XMP-photoshop:Source"] = $data['photoshop_source'];
		
		$bag->add(new Metadata(new IPTC_Source(), new Mono($data['photoshop_source'])));
		$metadata["IPTC:Source"] = $data['photoshop_source'];
	}
	// Description
	if(isset($data['description'])){
		// XMP
		$bag->add(new Metadata(new Description(), new Mono($data['description'])));
		$metadata["XMP-dc:Description"] = $data['description'];
		
		// IPTC
		$bag->add(new Metadata(new CaptionAbstract(), new Mono($data['description'])));
		$metadata["IPTC:Caption-Abstract"] = $data['description'];
		
	}
	// WebStatement
	if(isset($data['webstatement'])){
		// XMP
		$bag->add(new Metadata(new WebStatement(), new Mono($data['webstatement'])));
		$metadata["XMP-xmpRights:WebStatement"] = $data['webstatement'];
	}
	// Licensor URL
	if(isset($data['licensor_url'])){
		// XMP
		$bag->add(new Metadata(new LicensorURL(), new Mono($data['licensor_url'])));
		$metadata["XMP-plus:LicensorURL"] = $data['licensor_url'];
	}
	// Copyright
	if(isset($data['copyright_notice'])){
		// XMP
		$bag->add(new Metadata(new Rights(), new Mono($data['copyright_notice'])));
		$metadata["XMP-dc:Rights"] = $data['copyright_notice'];
		
		// IPTC
		$bag->add(new Metadata(new CopyrightNotice(), new Mono($data['copyright_notice'])));
		$metadata["IPTC:CopyrightNotice"] = $data['copyright_notice'];
	}
	// Credit
	if(isset($data['credit'])){
		// XMP
		$bag->add(new Metadata(new XMPCredit(), new Mono($data['credit'])));
		$metadata["XMP-photoshop:Credit"] = $data['credit'];
		
		// IPTC
		$bag->add(new Metadata(new Credit(), new Mono($data['credit'])));
		$metadata["IPTC:Credit"] = $data['credit'];
	}
	// Creator
	if(isset($data['creator'])){
		
		// XMP
		$bag->add(new Metadata(new Creator(), new Mono($data['creator'])));
		$metadata["XMP-dc:Creator"] = $data['creator'];
				
		// IPTC
		$bag->add(new Metadata(new ByLine(), new Mono($data['creator'])));
		$metadata["IPTC:By-line"] = $data['creator'];
	}
	// Keywords
	if(isset($data['keywords'])){
		
		// XMP
		$bag->add(new Metadata(new Subject(), new Mono($data['keywords'])));
		$metadata["XMP-dc:Subject"] = $data['keywords'];
		
		// IPTC
		$bag->add(new Metadata(new IPTC_Keywords(), new Mono($data['keywords'])));
		$metadata["IPTC:Keywords"] = $data['keywords'];
	}
	// ModifyDate
	if(isset($data['IFD0:ModifyDate'])){
		// ModifyDate
		$bag->add(new Metadata(new ModifyDate(), new Mono($data['IFD0:ModifyDate'])));
		$metadata["IFD0:ModifyDate"] = $data['IFD0:ModifyDate'];
	}
	if(isset($data['ExifIFD:ExifVersion'])){
		// ExifVersion
		$bag->add(new Metadata(new ExifVersion(), new Mono($data['ExifIFD:ExifVersion'])));
		$metadata["ExifIFD:ExifVersion"] = $data['ExifIFD:ExifVersion'];
	}
	if(isset($data['ExifIFD:DateTimeOriginal'])){
		// DateTimeOriginal
		$bag->add(new Metadata(new DateTimeOriginal(), new Mono($data['ExifIFD:DateTimeOriginal'])));
		$metadata["ExifIFD:DateTimeOriginal"] = $data['ExifIFD:DateTimeOriginal'];
	}
	if(isset($data['ExifIFD:CreateDate'])){
		// CreateDate
		$bag->add(new Metadata(new CreateDate(), new Mono($data['ExifIFD:CreateDate'])));
		$metadata["ExifIFD:CreateDate"] = $data['ExifIFD:CreateDate'];
	}
	if(isset($data['ExifIFD:OffsetTimeOriginal'])){
		// OffsetTimeOriginal
		$bag->add(new Metadata(new OffsetTimeOriginal(), new Mono($data['ExifIFD:OffsetTimeOriginal'])));
		$metadata["ExifIFD:OffsetTimeOriginal"] = $data['ExifIFD:OffsetTimeOriginal'];
	}
	if(isset($data['ExifIFD:OffsetTimeDigitized'])){
		// OffsetTimeDigitized
		$bag->add(new Metadata(new OffsetTimeDigitized(), new Mono($data['ExifIFD:OffsetTimeDigitized'])));
		$metadata["ExifIFD:OffsetTimeDigitized"] = $data['ExifIFD:OffsetTimeDigitized'];
	}
    
	/*if(isset($data['File:ImageWidth'])){
		// ImageWidth
		$bag->add(new Metadata(new ImageWidth(), new Mono($data['File:ImageWidth'])));
		$metadata["File:ImageWidth"] = $data['File:ImageWidth'];
	}
	if(isset($data['File:ImageHeight'])){
		// ImageHeight
		$bag->add(new Metadata(new ImageHeight(), new Mono($data['File:ImageHeight'])));
		$metadata["File:ImageHeight"] = $data['File:ImageHeight'];
	}*/
    
	if(isset($data['ExifIFD:OffsetTime'])){
		// OffsetTime
		$bag->add(new Metadata(new OffsetTime(), new Mono($data['ExifIFD:OffsetTime'])));
		$metadata["ExifIFD:OffsetTime"] = $data['ExifIFD:OffsetTime'];
	}
	if(isset($data['ExifIFD:ComponentsConfiguration'])){
		// ComponentsConfiguration
		$bag->add(new Metadata(new ComponentsConfiguration(), new Mono($data['ExifIFD:ComponentsConfiguration'])));
		$metadata["ExifIFD:ComponentsConfiguration"] = $data['ExifIFD:ComponentsConfiguration'];
	}
	if(isset($data['ExifIFD:FlashpixVersion'])){
		// FlashpixVersion
		$bag->add(new Metadata(new FlashpixVersion(), new Mono($data['ExifIFD:FlashpixVersion'])));
		$metadata["ExifIFD:FlashpixVersion"] = $data['ExifIFD:FlashpixVersion'];
	}
	if(isset($data['ExifIFD:ColorSpace'])){
		// ColorSpace
		$bag->add(new Metadata(new ColorSpace(), new Mono($data['ExifIFD:ColorSpace'])));
		$metadata["ExifIFD:ColorSpace"] = $data['ExifIFD:ColorSpace'];
	}
	
	
		
	// Apply to all attachments
	if(!empty($attachment_id)){
		
		if($scaled_only){
			$all_sizes = wpclink__get_scaled_image_only($attachment_id);
		}else{
		// All
			$all_sizes = wpclink_get_all_image_sizes($attachment_id);
		}
		foreach($all_sizes as $single_image){
			$writer->write($single_image, $bag);
		}
		
	}else{
		$writer->write($image_path, $bag);
	}
	
	
	// Cache
	if($cache_id > 0){
				
		if(is_array($metadata)){
			
			// Cache
			if($prev_metadata = get_post_meta($cache_id,'wpclink_media_cache_metadata',true)){
				if(is_array($prev_metadata)){
					// Update Existing Data
					$updated_data = array_replace($prev_metadata,$metadata);
					// Update
					update_post_meta($cache_id,'wpclink_media_cache_metadata',$updated_data);
				}
			}else{
				update_post_meta($cache_id,'wpclink_media_cache_metadata',$metadata);
			}
		}
	}
}
/**
 * CLink Get Exif Tool Image Medatadata 
 * 
 * @param string path image path
 * @param array field metadata fields
 *
 * @return string matadata tag 
 */
function wpclink_get_image_all_metadata_value($path = false){
	
$logger = new Logger('exiftool');
$reader = Reader::create($logger);
$metadataBag = $reader->files($path)->first();
foreach ($metadataBag as $metadata) {
	
    if (ValueInterface::TYPE_BINARY === $metadata->getValue()->getType()) {		
	$key_tag = (string)$metadata->getTag();
	$tags[$key_tag] = $metadata->getValue()->asString();
		
    } else {
	$key_tag = (string)$metadata->getTag();
	$tags[$key_tag] = $metadata->getValue()->asString();
			
		
    }
	
}
	return $tags;
	
}
/**
 * CLink ExifTool Binary Remove Prefix
 * 
 * @param array data 
 * @param string prefix
 *
 * @return array data
 */
function wpclink_exiftool_binary_remove_prefix($data = array(), $prefix = 'base64:'){
	
	$data_filter = array();
	
	foreach ($data as $key => $single){
		
		$data_filter[$key] = str_replace('base64:','',$single);
	}
	
	return $data_filter;
	
}
/**
 * CLink ExifTool Full Metadata
 * 
 * @param string image path
 *
 * @return array metadata
 */
function wpclink_exif_full_metadata($image_path, $box_num = '', $filter = false){
    
	$exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
    
    if(!empty($box_num)){
        
        $output = shell_exec('wget --no-check-certificate -qO - "'.add_query_arg( 'cache', uniqid(), $image_path).'" | '.$exiftool_file.' -G3 -struct -j -b -fast -');
        if(empty($output)) return false;
        $metadata_array = json_decode($output, true);
        
        $list = $metadata_array[0];
        
        $active_box_num = wpclink_active_manifest($image_path);
        
        if($active_box_num == $box_num ){        
            if(isset($list['Main:WebStatement'])){
                $rights_ID = $list['Main:WebStatement'];
            }
        }
        
        foreach ($list as $level_1_key => $level_1_val ){
            // Level 3 and 4
        if(preg_match('/Doc1-([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])/', $level_1_key, $output_full_meta)){  
                $level_4_array[$level_1_key] = $level_1_val; 
                // Box 
                $box[$output_full_meta[1]] = $output_full_meta[1];
            }
        }
        $list_full = $level_4_array;
        
        foreach($box as $box_key => $box_value){
            $ingredients = array();
            $list[$box_key] = wpclink_filter_me($list_full,'1-'.$box_value);
            if(!empty($rights_ID)){
                $list[$box_key]['Doc1-'.$box_value.':WebStatement'] = $rights_ID;
            }
            // Active Box
            $list[$box_key]['Doc1-'.$box_value.':ActiveBox'] = $active_box_num;
            
        }
        
        if($filter){
            
            $search_keys = array('Author','Actions','Item1','Claim_generator','WebStatement','ActiveBox');
            $filter_array = array();
            
             if(isset($list[$box_num])){
                
                 foreach($list[$box_num] as $filter_key => $filter_val){
                    
                     
                    $key = explode(':',$filter_key);
                    $key = $key[1];
                     
                    if(in_array($key,$search_keys)){
                        $filter_array[0][$key] = $filter_val;
                    } 
                    
                    
                     
                 }
                 
                 return $filter_array;
            }
            
            
        }else{
            if(isset($list[$box_num])){
                return $list[$box_num];
            }
        }
        
    }else{
        $output = shell_exec('wget --no-check-certificate -qO - "'.add_query_arg( 'cache', uniqid(), $image_path).'" | '.$exiftool_file.' -struct -b -j -fast -');
        if(empty($output)) return false;
        $metadata_array = json_decode($output, true);
        return $metadata_array;
    }
}
/**
 * CLink ExifTool Read XMP Metadata Filter
 * 
 * @param string image path
 * @param array metadata
 * @param array taglookup
 *
 * @return array metadata
 */
function wpclink_exif_read_xmp_metadata_filter( $image_path = '', $metadata_array = array(), $tagLookup){
	
	$binary_array = array();
		
	if($metadata_array == false) return false;
	
	foreach ($metadata_array  as $single_meta){
		if(is_array($metadata_array)){
			foreach ($single_meta as $key => $single){
				if(in_array($key,$tagLookup)){
					$binary_array[$key] = $single;
				}
			}
		}
	}
	return $binary_array;
}
/**
 * CLink Get Exif Tool Image Medatadata  Value
 * 
 * @param string path image path
 * @param array field metadata fields
 *
 * @return string matadata tag 
 */
function wpclink_get_image_metadata_value($path = false, $field = ''){
	
$logger = new Logger('exiftool');
$reader = Reader::create($logger);
$metadataBag = $reader->files($path)->first();
if(is_array($field)){
	$tags = array();
}else{
	$tags = '';
}
	
foreach ($metadataBag as $metadata) {
	
    if (ValueInterface::TYPE_BINARY === $metadata->getValue()->getType()) {
		
		if(is_array($field)){
			if(in_array($metadata->getTag(), $field)){
				$key_tag = (string)$metadata->getTag();
				$tags[$key_tag] = $metadata->getValue()->asString();
			}
		}else{
			if($metadata->getTag() == $field){
				return $metadata->getValue()->asString();
			}
		}
		
    } else {
		
		if(is_array($field)){
			
			if(in_array($metadata->getTag(), $field)){
				$key_tag = (string)$metadata->getTag();
				$tags[$key_tag] = $metadata->getValue()->asString();
			}
		}else{
			if($metadata->getTag() == $field){
				return $metadata->getValue()->asString();
			}
		}
    }
	
}
	return $tags;
	
}
/**
 * CLink Show Metadata Tree
 * 
 * @param string path image path
 * @param string time of modification
 * @param string recorded url full
 * @param integer metadata id
 *
 * @return string html
 */
function wpclink_show_metadata_tree($path, $time_of_modification, $recorded_url_full, $attachment_id = '0'){
    
    
    
$exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
$output = shell_exec(' wget --no-check-certificate --no-cache --no-cookies -qO - "'.add_query_arg( 'cache', uniqid(), $path).'" | '.$exiftool_file.' -G3 -struct -j -b -fast -');
if(empty($output)) return false;
    
$metadata_array = json_decode($output, true);
    
$list = $metadata_array[0];
$active_manifiest_id = wpclink_active_manifest($path);    
// Full
$full_ingredients = array();
// Level 3 and 4
$level_4_array = array();
// Box
$box = array();
    
if($type == 'archive'){
$extra_class = 'archive ';
}else{
$extra_class = 'ing ';
}
foreach ($list as $level_1_key => $level_1_val ){
    // Level 3 and 4
if(preg_match('/Doc1-([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])/', $level_1_key, $output_full_meta)){  
        $level_4_array[$level_1_key] = $level_1_val; 
     
        // Box 
        $box[$output_full_meta[1]] = $output_full_meta[1];
     
    }
    
}
        
$list_full = $level_4_array;
foreach($box as $box_key => $box_value){
   
    $ingredients = array();
    
    $list = wpclink_filter_me($list_full,'1-'.$box_value);
    foreach($list as $key => $list_single){
        if(!is_array($list_single)){
            if(preg_match('/c2pa.claim/', $list_single, $output_array)){
                    $current_doc = explode(':',$key);
                    $current_doc = $current_doc[0];
                    // Current Indgredient 0
                    $ingred_0 = $output_array[0];
                    // Title 
                    if(array_key_exists($current_doc.':Title', $list)){
                        $current_title = $list[$current_doc.':Title'];
                        // Set
                        $ingredients[$ingred_0]['Title'] = $current_title;
                    }
                    // Format 
                    if(array_key_exists($current_doc.':Format', $list)){
                        $current_format = $list[$current_doc.':Format'];
                        // Set
                        $ingredients[$ingred_0]['Format'] = $current_format;
                    }
                    $doc_key = wpclink_find_key_from_value('c2pa.thumbnail.claim.jpeg',$list);
                    if( $doc_key !== false){
                        $doc_key_num = explode(':',$doc_key);
                        if(isset($doc_key_num[0])){
                            $doc_key_num_found = $doc_key_num[0];
                            if(array_key_exists($doc_key_num_found.':C2paThumbnailClaimJpegData', $list)){
                                $doc_key_num_found_data = $doc_key_num_found.':C2paThumbnailClaimJpegData';
                                $ingredients[$ingred_0]['Data'] = $list[$doc_key_num_found_data];
                            }
                            if(array_key_exists($doc_key_num_found.':JUMDLabel', $list)){
                                $doc_key_num_found_label = $doc_key_num_found.':JUMDLabel';
                                $ingredients[$ingred_0]['Label'] = $list[$doc_key_num_found_label];
                            }
                        }
                    }
              }else if(preg_match('/c2pa[[\.\]]ingredient__([0-9]|[1-9][0-9]|[1-9][0-9][0-9])/', $list_single, $output_array)){ 
                    $current_doc = explode(':',$key);
                    $current_doc = $current_doc[0];
                    $current_number = $output_array[1];
                    // Current Indgredient 0
                    $ingred_0 = $output_array[0];
                    // Title 
                    if(array_key_exists($current_doc.':Title', $list)){
                        $current_title = $list[$current_doc.':Title'];
                        // Set
                        $ingredients[$ingred_0]['Title'] = $current_title;
                    }
                    // Format 
                    if(array_key_exists($current_doc.':Format', $list)){
                        $current_format = $list[$current_doc.':Format'];
                        // Set
                        $ingredients[$ingred_0]['Format'] = $current_format;
                    }
                    $doc_key = wpclink_find_key_from_value('c2pa.thumbnail.ingredient__'.$current_number.'.jpeg',$list);
                    if( $doc_key !== false){
                        $doc_key_num = explode(':',$doc_key);
                        if(isset($doc_key_num[0])){
                            $doc_key_num_found = $doc_key_num[0];
                            if(array_key_exists($doc_key_num_found.':C2paThumbnailIngredient_'.$current_number.'JpegData', $list)){
                                $doc_key_num_found_data = $doc_key_num_found.':C2paThumbnailIngredient_'.$current_number.'JpegData';
                                $ingredients[$ingred_0]['Data'] = $list[$doc_key_num_found_data];
                            }
                            if(array_key_exists($doc_key_num_found.':JUMDLabel', $list)){
                                $doc_key_num_found_label = $doc_key_num_found.':JUMDLabel';
                                $ingredients[$ingred_0]['Label'] = $list[$doc_key_num_found_label];
                            }
                        }
                    }
              }else if(preg_match('/c2pa[[\.\]]ingredient/', $list_single, $output_array)){
                    $current_doc = explode(':',$key);
                    $current_doc = $current_doc[0];
                    // Current Indgredient 0
                    $ingred_0 = $output_array[0];
                    // Title 
                    if(array_key_exists($current_doc.':Title', $list)){
                        $current_title = $list[$current_doc.':Title'];
                        // Set
                        $ingredients[$ingred_0]['Title'] = $current_title;
                    }
                    // Format 
                    if(array_key_exists($current_doc.':Format', $list)){
                        $current_format = $list[$current_doc.':Format'];
                        // Set
                        $ingredients[$ingred_0]['Format'] = $current_format;
                    }
                    $doc_key = wpclink_find_key_from_value('c2pa.thumbnail.ingredient.jpeg',$list);
                    if( $doc_key !== false){
                        $doc_key_num = explode(':',$doc_key);
                        if(isset($doc_key_num[0])){
                            $doc_key_num_found = $doc_key_num[0];
                            if(array_key_exists($doc_key_num_found.':C2paThumbnailIngredientJpegData', $list)){
                                $doc_key_num_found_data = $doc_key_num_found.':C2paThumbnailIngredientJpegData';
                                $ingredients[$ingred_0]['Data'] = $list[$doc_key_num_found_data];
                            }
                            if(array_key_exists($doc_key_num_found.':JUMDLabel', $list)){
                                $doc_key_num_found_label = $doc_key_num_found.':JUMDLabel';
                                $ingredients[$ingred_0]['Label'] = $list[$doc_key_num_found_label];
                            }
                        }
                    }
              }
        }
    }
 
    
    $full_ingredients[$box_value] = $ingredients;
    $full_ingredients[$box_value]['box_number'] = $box_value;
    
    }
    
    /*echo '<pre>';
    print_r($full_ingredients);
    echo '</pre>';*/
    
    $last_full_ingredient = end($full_ingredients);
    $prev_full_ingredient = prev($full_ingredients);
    
    
$full_metadata = wpclink_exif_full_metadata($path,$active_manifiest_id,true);
	
$html = '';
$size_square = '40px';
	
	$html.= '';
	
	$entry_node_0 = '';
		$time_of_modification = str_replace('Z','',$time_of_modification);
		$dateformate_0 = explode("T",$time_of_modification);
		
		if(strpos($time_of_modification,'T') == false){
			$time_of_sign_0 =  $time_of_modification;
		}else{
			$newDate_0 = date("M j, Y", strtotime($dateformate_0[0]));
			$newDate_0 .= ' at ';
			$newDate_0 .= date("h:i A", strtotime($dateformate_0[1]));
			$time_of_sign_0 =  $newDate_0;
		}
	
		$entry_node_0 .= '<div class="entry_node_wrapper clink-node">';
		$entry_node_0 .= '<span class="claim_generator">CLink </span>';
		$entry_node_0 .= '<span class="time_of_sign">'.$time_of_sign_0.' <a  target="_blank" href="'.$recorded_url_full.'" class="external"></a></span>';
		$entry_node_0 .= '</div>';
	
	$entry_node = '<div class="entry_node_wrapper">';
	
	if(isset($full_metadata[0]['Claim_generator']) and !empty($full_metadata[0]['Claim_generator'])){
		
		$claim_generator_words = explode(" ", $full_metadata[0]['Claim_generator']);
		
		$mono_title = substr($full_metadata[0]['Claim_generator'], 0, 1);
		
		$logo_select_class = implode(" ",array_slice($claim_generator_words,0,1));
		
		$entry_node .= '<span class="metadata_logo '.strtolower($logo_select_class).'"></span>';
		
	/*	$entry_node .= '<span class="mono_title">'.strtoupper($mono_title).'</span>';*/
		
		$entry_node .= '<span class="claim_generator">'.implode(" ",array_slice($claim_generator_words,0,1)) .'</span>';
	}
	
	if(isset($full_metadata[0]['Item1']['temp_signing_time']) and !empty($full_metadata[0]['Item1']['temp_signing_time'])){
		
		$originalDate = $full_metadata[0]['Item1']['temp_signing_time'];
		
		$dateformate = explode("T",$originalDate);
		if(strpos($originalDate,'T') == false){
			$time_of_sign =  $originalDate;
		}else{
			
					
			$filter_dateformate = explode('.',$dateformate[1]);
			
			$newDate = date("M j, Y", strtotime($dateformate[0]));
			$newDate .= ' at ';
			$newDate .= date("h:i A", strtotime($filter_dateformate[0]));
			$time_of_sign =  $newDate;
		}
		
		$entry_node .= '<span class="time_of_sign">'.$time_of_sign.'</span>';
	}
	
	
	$entry_node .='</div>';
		

	$entry_node_0_html = '<li><span class="data-node clink-node-0">'.$entry_node_0.'</span></li>';
    
    
    echo '<ul class="shorttree">';

    echo $entry_node_0_html;
    
    echo '<li>';
    
    echo '<span class="data-node"><span>';
    echo '<span class="c2pa-seal"></span>';
    $ingredient_claim_image_data = $last_full_ingredient['c2pa.claim']['Data'];
    $box_number = $last_full_ingredient['box_number'];
    
    // Claim
    if(isset($ingredient_claim_image_data)){
        echo '<img src="data:image/jpeg;base64,'.str_replace('base64:','',$ingredient_claim_image_data).'"  width="48px" height="48px">';
    }
   
    echo '</span>';
    echo '</span>';
    
    echo $entry_node;
    
    echo '</span>';
    
     
    
    echo '</li>';
    
    
    
     $circle_count = 0;
     $print_circle = '';
     $count_images = 0;
    
 
    
    // Before 
     foreach($last_full_ingredient as $ingredient_block_key => $ingredient_block_val){
         
         // Not Claim
        if($ingredient_block_key == 'c2pa.claim' || $ingredient_block_key == 'box_number' ){
            // Not above keys
        }else{
         
          if(isset($ingredient_block_val['Data'])){
                $print_circle .='<span class="circle circlenum-'.$circle_count.'"></span>';
				$circle_count++;
          }
            
        }
       
          
       
         
     }
    
    $circles.= '<span class="circles">'.$print_circle .'<div class="round-1 round"></div><div class="round-2 round"></div></span>';
        
    
    // Claim    
   echo '<ul class="node-count-'.$circle_count.' nodes"><li>'.$circles.'<span class="data-node">';
    
    
    foreach($last_full_ingredient as $ingredient_block_key => $ingredient_block_val){
        
        
        
        // Not Claim
        if($ingredient_block_key == 'c2pa.claim' || $ingredient_block_key == 'box_number' ){
            // Not above keys
        }else{
            
            
            $count_images++;
            
            if( $count_images > 3) break;
            
            
            
            
            $seal = '';
            
            // Find Ingredient Before Arrow
            if(isset($ingredient_block_val['Title'])){
                if(isset($prev_full_ingredient['c2pa.claim']['Title'])){
                    if($prev_full_ingredient['c2pa.claim']['Title'] == $ingredient_block_val['Title']){
                        
                        $box_number_level_2 = $prev_full_ingredient['box_number'];
                        
                         foreach($prev_full_ingredient as $prev_full_ingredient_key => $prev_full_ingredient_val){
                            if($prev_full_ingredient_key == 'c2pa.claim' || $prev_full_ingredient_key == 'box_number'){
                            }else{
                                
                            }
                        }
                        
                        $seal = '<span class="c2pa-seal"></span>';
                        
                        
                        $image_atr = '';
                    }
                }
            }
            
           
            
            // Image
           if(isset($ingredient_block_val['Data'])){
               echo '<span>';
               echo $seal;
                echo '<img  src="data:image/jpeg;base64,'.str_replace('base64:','',$ingredient_block_val['Data']).'" width="48px" height="48px" '.$image_atr.'>';
               
                echo '</span>';
           }
           
           
            
            // Find Ingredient
            if(isset($ingredient_block_val['Title'])){
                if(isset($prev_full_ingredient['c2pa.claim']['Title'])){
                    if($prev_full_ingredient['c2pa.claim']['Title'] == $ingredient_block_val['Title']){
                        
                
                       
                        foreach($prev_full_ingredient as $prev_full_ingredient_key => $prev_full_ingredient_val){
                            
                            
                            
                            if($prev_full_ingredient_key == 'c2pa.claim' || $prev_full_ingredient_key == 'box_number'){
                                // Not above keys
                            }else{
                                
                              
                                
                                    echo '<span>';
            
                                        // Image
                                       if(isset($prev_full_ingredient_val['Data'])){
                                            echo '<img  src="data:image/jpeg;base64,'.str_replace('base64:','',$prev_full_ingredient_val['Data']).'" width="48px" height="48px">';
                                       }
                                        
                                
                                    echo '</span>';
                                
                                
                            }
                        }
                    }
                }
            }
            
            
            
            
        }
        
    }
    
    echo '</span></li></ul>';
    
    echo '</ul>';
    
    
    
    
    
    
reset($full_ingredients);

	
	$html.= '<div class="tree_button_wrapper"><a data-attach-id="'.$attachment_id.'" data-box-num='.$box_number.' class="tree-view-more" >View more</a></div>';
	
	
	echo $html;
	
}
/**
 * CLink Metadata Detailed
 * 
 * @param path url
 *
 * @return null
 */
function wpclink_metadata_detailed($path = ''){
	$full_metadata = wpclink_exif_full_metadata($path);
}
/**
 * CLink Metadata image Icon
 * 
 * @param string image url
 *
 * @return string image html
 */
function wpclink_metata_icon_image($image_filname){
    
    if($image_filname == 'combine_1.svg'){
         $size = '22px';
    }else if($image_filname == 'color_1.svg'){
         $size = '20px';
    }else{
         $size = '18px';
    }
    
	$directory = plugin_dir_url( WPCLINK_MAIN_FILE ).'public/images/metadata/';
	$image = '<img src="'.$directory.$image_filname.'" width="'.$size.'" height="'.$size.'" />';
	return $image;
}
/**
 * CLink Show Metadata List
 * 
 * @param string path
 *
 * @return string image metadata html
 */
function wpclink_show_metadata_list($path = '', $box_number = ''){
    
     
            // Ingredient Manifiest
            
            if(!empty($box_number)){
                $active_manifiest = wpclink_active_manifest($path);
                if($box_number == $active_manifiest){
                    // Show all Boxes
                }else{
                    $html .= '<div class="full-metadata-wrapper">';
	                $html .= '<table class="full-metadata jumbf_only">';
                    
                    ob_start();
                    wpclink_read_metadata_g3_advance($path, $box_number );
                    $output = ob_get_contents();
                    ob_end_clean();
                    
                    $html.= '<tr class="subheader"><td colspan="2">'.$output.'</td></tr>';
                    
                    $html .= '</div>';
	                $html .= '</table>';
                    
                    return $html;
                }
            }
            
            // End
    
    
	$exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
	$output = shell_exec(' wget --no-check-certificate -qO - "'.add_query_arg( 'cache', uniqid(), $path).'" | '.$exiftool_file.' -G -j -fast -');
	$all_metadata_full = json_decode($output, true);
	$buttons = array();
	foreach($all_metadata_full as $all_metadata){ 
		foreach($all_metadata as $meta_single_key => $meta_single_val){
		
			$metadata_key_btn = explode(':',$meta_single_key);
			$metadata_key_btn_name = $metadata_key_btn[0];
			array_push($buttons, $metadata_key_btn_name);
			
			
		}
	}
	$buttons = array_unique($buttons);
	asort($buttons);
	$button_link = '<li><a class="metadata_action_select" data-selected="0" data-group="all">Select All</a></li>';
	foreach($buttons as $single_button){
		if(!empty($single_button)){
			$button_link .= '<li><a class="metadata_filter_btn selected '.strtolower($single_button).'" data-selected="1" data-group="'.strtolower($single_button).'">'.strtoupper($single_button).'</a></li>';
		}
	}
    
     $ref_keys = array(
        'By-line' => 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#creator',
        'Caption-Abstract' => 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#description',
        'CopyrightNotice' => 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#copyright-notice',
        'Credit' => 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#credit-line',
        'Headline' => 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#headline',
        'Keywords' => 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#keywords',
        'ObjectName' => 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#title',
        'Source' => 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#source',
        'Writer Editor' => 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#description-writer'
    );
    
	$button_link .= '<li><a class="metadata_action_clear" data-selected="0" data-group="none">Clear All</a></li>';
	$html = '';
	$html .= '<div class="action_filter_btn"><ul class="metadata-menu"><li><a>Select Tags</a><ul>'.$button_link.'</ul><li></ul></div>';
	$html .= '<div class="full-metadata-wrapper">';
	$html .= '<table class="full-metadata">';
	foreach($all_metadata_full as $all_metadata){ 
		ksort($all_metadata);
		$subheader = '';
		foreach($all_metadata as $single_key => $single_val){
			$metadata_key = explode(':',$single_key);
			$metadata_group = $metadata_key[0];
            $keyname = $metadata_key[1];
			if(!empty($metadata_group)){
				$class = strtolower($metadata_group);
			}else{
				$class = 'unknown';
			}
            
           
            
            
            
			if($subheader != $metadata_group){
                
                if($metadata_group == 'JUMBF'){
                    $expand_btn = ' <span class="expand-boxes" data-expand="1">Collapse</span>';
                }else{
                     $expand_btn = '';
                }
				$html.= '<tr class="subheader '.$class.'"><td colspan="2">'.str_replace("IPTC","IPTC (IIM)",strtoupper($metadata_group)).$expand_btn.'</td></tr>';
				$subheader = $metadata_group;
                
                if($metadata_group == 'JUMBF'){
                    
                    ob_start();
                    wpclink_read_metadata_g3_advance($path, $box_number );
                    $output = ob_get_contents();
                    ob_end_clean();
                    
                    
                    //$output = wpclink_metadata_list_g3($path);
                    
                    
                    $html.= '<tr class="subheader '.$class.'"><td colspan="2">'.$output.'</td></tr>';
                }
			}
            
            
            if($metadata_group == 'JUMBF'){
                continue;   
            }
			$single_val_arr = '';
            
            if($single_key == 'XMP:RegistryItemID' || $single_key == 'XMP:RegistryEntryRole'){
                
                if(is_array($single_val)){
                    
                    foreach($single_val as $key => $xml_val){
                        
                        
                    $find_site_1 = "https://clink.id";
                    $find_site_2 = "https://cordra.dev";
                    $find_site_3 = "https://licenses.clink.id";
                    $find_site_4 = "https://wptst.site";
                    
					$found_1 = strpos($single_val_arr_check, $find_site_1);
                    $found_2 = strpos($single_val_arr_check, $find_site_2);
                    $found_3 = strpos($single_val_arr_check, $find_site_3);
                    $found_4 = strpos($single_val_arr_check, $find_site_4);
					if ($found_1 === false && 
                        $found_2 === false && 
                        $found_3 === false && 
                        $found_4 === false ) {
                        
					}else{
						$xml_val = sprintf('%s <a target="_blank" href="%s" class="external"></a>',str_replace(array("https://cordra.dev/#objects/","https://clink.id/#objects/"),"",$xml_val), $xml_val);
					}
                        
                        
                        $single_val_arr .= ''.$xml_val.'<br />';
                    }
                    
                  
                }
                
            }else{
			if(is_array($single_val)){
				foreach($single_val as $new_single_val){
					$single_val_arr_check = $new_single_val;
					$find_site_1 = "https://clink.id";
                    $find_site_2 = "https://cordra.dev";
                    $find_site_3 = "https://licenses.clink.id";
                    $find_site_4 = "https://wptst.site";
                    
					$found_1 = strpos($single_val_arr_check, $find_site_1);
                    $found_2 = strpos($single_val_arr_check, $find_site_2);
                    $found_3 = strpos($single_val_arr_check, $find_site_3);
                    $found_4 = strpos($single_val_arr_check, $find_site_4);
					if ($found_1 === false && $found_2 === false && $found_3 === false && $found_4 === false ) {
					}else{
						$single_val_arr_check = sprintf('%s <a target="_blank" href="%s" class="external"></a><br />',str_replace(array("https://cordra.dev/#objects/","https://clink.id/#objects/"),"",$single_val_arr_check), $single_val_arr_check);
					}
				}
				$single_val_arr .= $single_val_arr_check;
				
			}else{
				$single_val_arr = $single_val;
                
                    $find_site_1 = "https://clink.id";
                    $find_site_2 = "https://cordra.dev";
                    $find_site_3 = "https://licenses.clink.id";
                    $find_site_4 = "https://wptst.site";
                    
					$found_1 = strpos($single_val_arr, $find_site_1);
                    $found_2 = strpos($single_val_arr, $find_site_2);
                    $found_3 = strpos($single_val_arr, $find_site_3);
                    $found_4 = strpos($single_val_arr, $find_site_4);
                if ($found_1 === false && $found_2 === false && $found_3 === false && $found_4 === false ) {
				}else{
					$single_val_arr = sprintf('%s <a target="_blank" href="%s" class="external"></a>',str_replace(array("https://cordra.dev/#objects/","https://clink.id/#objects/"),"",$single_val_arr), $single_val_arr);
				}
			}
                
                
            }
			
            $property = str_replace($metadata_group.":",'',$single_key);
        
            if(array_key_exists($property, $ref_keys)){
                if(!empty($ref_keys[$property])){
                    $eye = '<a target="_blank" class="eyebox" href="'.$ref_keys[$property].'"><span class="eye"></span></a>';
                }else{
                    $eye = '';
                }
            }else{
                $eye = '';
            }
          
           // Binary Filter
             $binary_data = strpos($single_val_arr, 'Binary data');
            if($binary_data !== false){
                
                
            $attr_key = explode(':',$single_key);
            if(isset($attr_key[1])){
                $attr_key = 'Main:'.$attr_key[1]; 
            }else{
                 $attr_key = $single_key;
            }
                
                if(!empty($attr_key)){
                    $attr = 'data-key="'.$attr_key.'"';
                }else{
                   $attr = ''; 
                }
                
                $single_val_arr = str_replace(array(', use -b option to extract','(',')'),' ',$single_val_arr);
                
                $single_val_arr = $single_val_arr.'<a '.$attr.' class="load-binary">View</a><a class="copyme">Copy</a><textarea readonly class="view-binary"></textarea>';
            }
        
            
            $html .= '<tr  class="'.$class.' metadata-field" ><td width="30%">'.str_replace($metadata_group.":",' ',$single_key).'</td><td  width="70%">'.$eye.$single_val_arr.'</td></tr>';
		}
	}
	$html .= '</table>';
	$html .= '</div>';
	return $html;
}
/**
 * CLink Show Metadata List Binary
 * 
 * @param string path
 * @param string target of metadata
 *
 * @return array metadata
 */
function wpclink_show_metadata_list_binary($path = '', $target = ''){
	$exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
	$output = shell_exec(' wget --no-check-certificate -qO - "'.add_query_arg( 'cache', uniqid(), $path).'" | '.$exiftool_file.' -G3 -j -b -fast -');
    
    
    $target_metadata_array = explode(',',$target);
    
    if(isset($target_metadata_array[1])){
        
        $all_metadata_full = json_decode($output, true);
   
        $complete_metadata  =  $all_metadata_full[0];
        $complete_metadata_new = array_change_key_case($complete_metadata, CASE_UPPER);
        $parent = strtoupper($target_metadata_array[0]);
        $child = strtoupper($target_metadata_array[1]);
        if(isset($complete_metadata_new[$parent][$child])){
           return $complete_metadata_new[$parent][$child];
        }
        
    }else{
	$all_metadata_full = json_decode($output, true);
   
    $complete_metadata  =  $all_metadata_full[0];
    $complete_metadata_new = array_change_key_case($complete_metadata, CASE_UPPER);
    
    $find = strtoupper($target);
    
      
    if(isset($complete_metadata_new[$find])){
       return $complete_metadata_new[$find];
    }
        
    }
    
    return false;
    
}
/**
 * CLink Show Metadata List Binary
 * 
 * @param string url
 *
 * @return string html
 */
function wpclink_metadata_url_to_image($url = ""){
	$social_media = array(
		'twitter.com' => 'twitter.svg',
		'facebook.com' => 'facebook.svg',
		'behance.net' => 'behance.svg'
	);
	$host =  str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
	if(array_key_exists($host,$social_media)){
		$directory = plugin_dir_url( WPCLINK_MAIN_FILE ).'public/images/';
		return '<span class="social-img"><img src="'.$directory.$social_media[$host].'" width="16px" height="16px" /></span>';
	}else{
		return false;
	}
}
/**
 * CLink Metadata Level 3
 * 
 * @param string image path
 * @param string creator URL
 * @param integer attachment id
 * @param string type
 * @param string archive time
 *
 * @return string html
 */
function wpclink_metadata_level_3($path = '', $creator_ID_url, $recorded_url_full, $attach_id = 0, $type = 'ingredients', $archive_time_pick = '', $box_number = ''){
    
    		
    
$full_metadata = wpclink_exif_full_metadata($path, $box_number, true);
    
if($type == 'archive'){
	$time_of_modification = $archive_time_pick;
}else{
	// Created DAte
	$get_post_date  = get_the_date('Y-m-d',$attach_id);
	$get_post_date .= 'T';
	$get_post_date .= get_the_date('G:i:s',$attach_id);
	$get_post_date .= 'Z';
	$time_of_modification = $get_post_date;
}
$time_of_modification = str_replace('Z','',$time_of_modification);
$dateformate_0 = explode("T",$time_of_modification);
if(strpos($time_of_modification,'T') == false){
	$time_of_sign_0 =  $time_of_modification;
}else{
	$newDate_0 = date("M j, Y", strtotime($dateformate_0[0]));
	$newDate_0 .= ' at ';
	$newDate_0 .= date("h:i A", strtotime($dateformate_0[1]));
	$time_of_sign_0 =  $newDate_0;
}
if(isset($full_metadata[0]['ActiveBox'])){
    $active_box = $full_metadata[0]['ActiveBox'];
}
$padding_box = 'padding_box';    
    
if(!empty($recorded_url_full)){
    if($active_box == $box_number || empty($box_number)  ){
        $entry_node_0 .= '<div class="level_3_identifier level_3_block">';
        $entry_node_0 .= '<span class="claim_generator">CLink </span>';
        $entry_node_0 .= '<span class="time_of_sign">'.$time_of_sign_0.' <a  target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$recorded_url_full.'" class="external"></a></span>';
        $entry_node_0 .= '</div>';
        $padding_box = '';
    }
}
    
    
echo $entry_node_0;
$signed_by = '';
if(isset($full_metadata[0]['Claim_generator']) and !empty($full_metadata[0]['Claim_generator'])){
		
	$claim_generator_words = explode(" ", $full_metadata[0]['Claim_generator']);
	
	$mono_title = substr($full_metadata[0]['Claim_generator'], 0, 1);
	
	$logo_select_class = implode(" ",array_slice($claim_generator_words,0,1));
	$signed_by .= '<li>';
	
	$signed_by .= '<span class="metadata_logo '.strtolower($logo_select_class).'"></span>';
	
	
	$signed_by .= '<span class="signed_by">'.implode(" ",array_slice($claim_generator_words,0,1)) .'</span>';
	$signed_by .= '</li>';
}
if(isset($full_metadata[0]['Item1']['temp_signing_time']) and !empty($full_metadata[0]['Item1']['temp_signing_time'])){
		
	$originalDate = $full_metadata[0]['Item1']['temp_signing_time'];
	
	$dateformate = explode("T",$originalDate);
	if(strpos($originalDate,'T') == false){
		$time_of_sign =  $originalDate;
	}else{
		
				
		$filter_dateformate = explode('.',$dateformate[1]);
		
		$newDate = date("M j, Y", strtotime($dateformate[0]));
		$newDate .= ' at ';
		$newDate .= date("h:i A", strtotime($filter_dateformate[0]));
		$time_of_sign =  $newDate;
	}
	$signed_on .= '<li>';
	$signed_on .= '<span class="signed_on">'.$time_of_sign.'</span> ';
	$signed_on .= '</li>';
}
$signed_block = '<div class="level_3_block_signed '.$padding_box.' level_3_block"><li><span class="meta_subheading">SIGNED BY <span class="infopopup"><span class="infdes">The entitiy that recorded the content credentials</span></span></span></li>'.$signed_by.$signed_on.'</div>';
echo $signed_block;
$produced_with = '';
	
	if(isset($full_metadata[0]['Claim_generator']) and !empty($full_metadata[0]['Claim_generator'])){
		
		$claim_generator_words = explode(" ", $full_metadata[0]['Claim_generator']);
		$mono_title = substr($full_metadata[0]['Claim_generator'], 0, 1);
		$logo_select_class = implode(" ",array_slice($claim_generator_words,1,1));
		
		$produced_with .= '<li><span class="meta_subheading">PRODUCED WITH <span class="infopopup"><span class="infdes">Software used to produce this content</span></span></span></li>';
		$produced_with .= '<li><span class="metadata_logo '.strtolower($logo_select_class).'"></span>';
		$produced_with .= '<span class="claim_generator">'.implode(" ",array_slice($claim_generator_words,0,3)) .'</span>';
		$produced_with .= '</li>';
	}
	
echo '<div class="level_3_block_produced_with level_3_block">'.$produced_with.'</div>';
$actions_list = array(
	'place_embedded_smart_object',
	'move',
	'free_transform',
	'layer_visibility',
	'select_and_mask',
	'add_layer_mask',
	'brush_tool',
	'enable_layer_mask',
	'paint_bucket',
	'gradient',
	'blending_change',
	'invert',
	'new_black__white_layer',
	'duplicate_layer',
	'bring_to_front',
	'delete_layer',
	'new_color_fill_layer',
	'modify_color_fill_layer'
);
	
	
$actions_label = array(
	'place_embedded_smart_object' => 'import asset',
	'move' 				=> 'change position',
	'free_transform' 	=> 'transform size',
	'layer_visibility' 	=> 'set visibility',
	'select_and_mask' 	=> 'set mask',
	'add_layer_mask'	=> 'add mask',
	'brush_tool' 		=> 'paint',
	'enable_layer_mask' => 'set mask',
	'paint_bucket' 		=> 'color tone',
	'gradient' 			=> 'set gradient',
	'blending_change' 	=> 'adjust colors',
	'invert' 			=> 'invert color',
	'new_black__white_layer' => 'add layer',
	'duplicate_layer' 	=> 'layer',
	'bring_to_front'	=> 'move',
	'delete_layer' 		=> 'delete layer',
	'new_color_fill_layer' => 'color change',
	'modify_color_fill_layer' => 'modify color',
);
	
$groups = array( 
	'color_adjustment' => array(
							'paint_bucket',
							'gradient',
							'invert',
							'new_color_fill_layer',
							'modify_color_fill_layer',
							'blending_change'),
	
	'combine_assets'	=> array(
							'select_and_mask',
							'add_layer_mask',
							'enable_layer_mask',
							'new_black__white_layer',
							'duplicate_layer',
							'bring_to_front',
							'delete_layer'),
	
	'import_assets'		=>	array(
							'place_embedded_smart_object'),
	
	'paint_tool'		=>	array(
							'brush_tool'),
	
	'size_and_position'	=>	array(
							'move',
							'free_transform',
	)
);
$group_color_adjustment = array();
$group_combine_assets = array();
$group_import_assets = array();
$group_paint_tool = array();
$group_size_and_position = array();
	
	
$group_color_adjustment_label = array();
$group_combine_assets_label = array();
$group_import_assets_label = array();
$group_paint_tool_label = array();
$group_size_and_position_label = array();
	
	
if(isset($full_metadata[0]['Actions'])){
	
	foreach ($full_metadata[0]['Actions'] as $single_action){
		
		if(in_array($single_action['parameters'], $actions_list)){
			
			if(in_array($single_action['parameters'],$groups['color_adjustment'])){
				
				$group_color_adjustment[] = $single_action['parameters'];
				$group_color_adjustment_label[] = $actions_label[$single_action['parameters']];
				
			}else if(in_array($single_action['parameters'],$groups['combine_assets'])){
				
				$group_combine_assets[] = $single_action['parameters'];
				$group_combine_assets_label[] = $actions_label[$single_action['parameters']];
				
			}else if(in_array($single_action['parameters'],$groups['import_assets'])){
				
				$group_import_assets[] = $single_action['parameters'];
				$group_import_assets_label[] = $actions_label[$single_action['parameters']];
				
			}else if(in_array($single_action['parameters'],$groups['paint_tool'])){
				
				$group_paint_tool[] = $single_action['parameters'];
				$group_paint_tool_label[] = $actions_label[$single_action['parameters']];
				
			}else if(in_array($single_action['parameters'],$groups['size_and_position'])){
				
				$group_size_and_position[] = $single_action['parameters'];
				$group_size_and_position_label[] = $actions_label[$single_action['parameters']];
				
			}
		}
		
	}
	
}
	
$icon_list = array(
	'color_adjustment' => 'color_1.svg',
	'combine_assets' => 'combine_1.svg',
	'import_assets' => 'import_1.svg',
	'paint_tool' => 'paint_1.svg',
	'size_and_position' => 'size_1.svg'
);
echo '<div class="level_3_block_edits_activities level_3_block"><li><span class="meta_subheading">EDITS AND ACTIVITY <span class="infopopup"><span class="infdes">Changes and actions to produce this content</span></span></span></li>';
	
	$list = '';
    $activity_count = 0;
	
	if(!empty($group_color_adjustment)){
        
    $activity_count++;
		
	if(count($group_color_adjustment_label) > 3){
		$etc_0 = ' etc..';
	}else{
		$etc_0 = '';
	}
		
	$list .= '<li> <span class="image_action">'.wpclink_metata_icon_image($icon_list['color_adjustment']).'</span> <span class="action-label">Color adjustments</span> <span class="action-description">'.ucfirst(implode(', ', array_slice($group_color_adjustment_label,0,3))).$etc_0.'</span> </li>';
		
	}
			
	if(!empty($group_combine_assets)){
        
    $activity_count++;
	
	if(count($group_combine_assets_label) > 3){
		$etc_1 = ' etc..';
	}else{
		$etc_1 = '';
	}
		
	$list .= '<li><span class="image_action">'.wpclink_metata_icon_image($icon_list['combine_assets']).'</span> <span class="action-label">Combined assets</span> <span class="action-description">'.ucfirst(implode(', ', array_slice($group_combine_assets_label,0,3))).$etc_1.'</span></li>';
		
	}
		
	if(!empty($group_import_assets)){
        
    $activity_count++;
		
	if(count($group_import_assets_label) > 3){
		$etc_2 = ' etc..';
	}else{
		$etc_2 = '';
	}
		
	$list .= '<li><span class="image_action">'.wpclink_metata_icon_image($icon_list['import_assets']).'</span> <span class="action-label">Imported assets</span> <span class="action-description">'.ucfirst(implode(', ', array_slice($group_import_assets_label,0,3))).$etc_2.'</span></li>';
		
	}
		
	if(!empty($group_paint_tool)){
        
    $activity_count++;
	
	if(count($group_paint_tool_label) > 3){
		$etc_3 = ' etc..';
	}else{
		$etc_3 = '';
	}	
		
	$list .= '<li><span class="image_action">'.wpclink_metata_icon_image($icon_list['paint_tool']).'</span> <span class="action-label">Paint tools</span> <span class="action-description">'.ucfirst(implode(', ', array_slice($group_paint_tool_label,0,3))).$etc_3.'</span></li>';
		
		
	}
			
	if(!empty($group_size_and_position)){
        
    $activity_count++;
		
	if(count($group_size_and_position_label) > 3){
		$etc_4 = ' etc..';
	}else{
		$etc_4 = '';
	}	
		
	$list .= '<li><span class="image_action">'.wpclink_metata_icon_image($icon_list['size_and_position']).'</span> <span class="action-label">Size and position adjustments</span> <span class="action-description">'.ucfirst(implode(', ', array_slice($group_size_and_position_label,0,2))).$etc_4.'</span></li>';
		
	}
    
    if($activity_count == 0){
        $list .= '<li>No Activity</li>';
    }
		
	$list .= '</div>';
	
	echo $list;
	
	$social_links = '';
	if(isset($full_metadata[0]['Author'])){
	
		if(is_array($full_metadata[0]['Author'])){
	
			$metadata_author = $full_metadata[0]['Author'];
			
			foreach($metadata_author as $author){
				
				if($author['@type'] == "Person"){
					if(array_key_exists("@id", $author)){
	
					$social_links.=	sprintf('<span class="social-icon"><a target="_blank" href="%s">%s</a></span>',$author["@id"],wpclink_metadata_url_to_image($author["@id"]));
	
					}
	
					
	
					//print_r($author);
				}
			
	
			}
	
		}
	
	}
	
if(isset($full_metadata[0]['Author'])){
	echo '<div class="level_3_block_produced_by level_3_block">';
	echo '<li><span class="meta_subheading">AUTHOR <span class="infopopup"><span class="infdes">Dispay name of the party created this content and link to ledger records</span></span></span>  </li>';
	if(is_array($full_metadata[0]['Author'])){
		$metadata_author = $full_metadata[0]['Author'];
        
        if(!isset($metadata_author['name'])){
            foreach($metadata_author as $author){
                if(isset($author['credential'])){
                    if(array_key_exists("name", $author)){
                        printf('<li>%s <a  target="_blank" href="%s" class="external"></a> %s</li>',$author["name"], $creator_ID_url, $social_links);
                    }
                    //print_r($author);
                }
            }       
        }else{
            
            $author = $full_metadata[0]['Author'];
            
            if(isset($author['credential'])){
                    if(array_key_exists("name", $author)){
                        printf('<li>%s <a  target="_blank" href="%s" class="external"></a> %s</li>',$author["name"], $creator_ID_url, $social_links);
                    }
                    //print_r($author);
                }
            
            
        }
		
	}
	echo '</div>';
}
    
// Rights 
if(!empty($full_metadata[0]['WebStatement'])){
    
    $wpclink_right_ID = $full_metadata[0]['WebStatement'];
	$right_ID_url = $wpclink_right_ID;
	
		
	if($right_time = get_post_meta($attach_id,'wpclink_right_created_time',true)){
		$created_time_right = $right_time;
	}
$created_time_right = str_replace('Z','',$created_time_right);
$dateformate_1 = explode("T",$created_time_right);
if(strpos($created_time_right,'T') == false){
	$time_of_sign_1 =  $created_time_right;
}else{
	$newDate_1 = date("M j, Y", strtotime($dateformate_1[0]));
	$newDate_1 .= ' at ';
	$newDate_1 .= date("h:i A", strtotime($dateformate_1[1]));
	$time_of_sign_1 =  $newDate_1;
}
	
	
	if(!empty($created_time_right) && !empty($right_time)){
		echo '<div class="level_3_block_rights level_3_block">';
		echo '<li><span class="meta_subheading">RIGHTS <span class="infopopup"><span class="infdes">Date the Right object associated with this content was recorded and link to ledger records</span></span></span>  </li><li>'.$time_of_sign_1.'<a href="'.$right_ID_url.'" class="external" target="_blank"></a></li>';
		echo '</div>';
	}else if(!empty($right_ID_url)){
        echo '<div class="level_3_block_rights level_3_block">';
		echo '<li><span class="meta_subheading">RIGHTS <span class="infopopup"><span class="infdes">Date the Right object associated with this content was recorded and link to ledger records</span></span></span>  </li><li>Rights <a href="'.$right_ID_url.'" class="external" target="_blank"></a></li>';
		echo '</div>';
    }
		
	}
	
}
/**
 * CLink Show Metadata Tree Short
 * 
 * @param string image path
 * @param integer attachment id
 * @param string archive time
 * @param string type
 *
 * @return string html
 */
function wpclink_show_metadata_tree_short($path, $attachment_id = 0, $archive_time_set = '', $type = 'ing'){
	
	$tagLookup = array(
		'C2paThumbnailClaimJpegData',
		'C2paThumbnailIngredientJpegData',
		'C2paThumbnailIngredient_1JpegData',
		'C2paThumbnailIngredient_2JpegData',
		'C2paThumbnailIngredient_3JpegData',
		'FileName',
		'Title',
		'Claim_generator',
		'Item1'
	);
	
	
		
	$full_metadata = wpclink_exif_full_metadata($path);
    
    $title_ing = wpclink_get_metadata_g3($path);
	
	
	
		
	$binary_data_img = wpclink_exiftool_binary_remove_prefix(wpclink_exif_read_xmp_metadata_filter($path, $full_metadata, $tagLookup));
		
	
	if($file_name = get_post_meta($attachment_id,'wpclink_filename',true)){
	}else{
		$file_name = 'N/A';
	}
	
		
	$tagtitle = array(
		'C2paThumbnailClaimJpegData' => $file_name,
		'C2paThumbnailIngredientJpegData' => $title_ing['c2pa.ingredient'],
		'C2paThumbnailIngredient_1JpegData' => $title_ing['c2pa.ingredient__1'],
		'C2paThumbnailIngredient_2JpegData' => $title_ing['c2pa.ingredient__2'],
		'C2paThumbnailIngredient_3JpegData' => $title_ing['c2pa.ingredient__3'],
		
	);
	
	$taglavel = array();
	if(isset($full_metadata[0]['C2paThumbnailClaimJpegData'])){
		$taglavel = array('C2paThumbnailClaimJpegData');
	}
		
	if(isset($full_metadata[0]['C2paThumbnailIngredientJpegData'])){
	$taglavel = array_merge($taglavel, array('C2paThumbnailIngredient' => array(
			'C2paThumbnailIngredientJpegData'
		)));
	}
	$arrow_down = '';
	
		
	if(isset($full_metadata[0]['C2paThumbnailIngredient_1JpegData'])){
		$arrow_down = '<span class="arrow_down"></span>'; 
		
		$taglavel['C2paThumbnailIngredient'] = array_merge($taglavel['C2paThumbnailIngredient'], array('C2paThumbnailIngredient_1JpegData'));
	
	}
		
		
	if(isset($full_metadata[0]['C2paThumbnailIngredient_2JpegData'])){
		
		$arrow_down = '<span class="arrow_down"></span>';
		
		$taglavel['C2paThumbnailIngredient'] = array_merge($taglavel['C2paThumbnailIngredient'], array('C2paThumbnailIngredient_2JpegData'));
	
	}
    
    
    if(isset($full_metadata[0]['C2paThumbnailIngredient_3JpegData'])){
		
		$arrow_down = '<span class="arrow_down"></span>';
		
		$taglavel['C2paThumbnailIngredient'] = array_merge($taglavel['C2paThumbnailIngredient'], array('C2paThumbnailIngredient_3JpegData'));
	
	}
	if($type == 'archive'){
		$extra_class = 'archive ';
	}else{
		$extra_class = 'ing ';
	}
		
	$size_square = '42px';
		
		echo '<ul class="'.$extra_class.'longtree">';
		
	
	
	
		
		$level_1_count = 0;
		
		if(empty($taglavel)){
            
           if($type == 'archive'){
			echo '<span class="archive_empty">No Archive</span>';
           }else{
             echo '<span>No C2PA Data</span>';  
           }
		}else{
		
		
		foreach ($taglavel as $tagkey => $tagval){
			
			if(is_array($tagval)){
				
			
				
				
				
				echo '<ul class="image_childs">';
	
		
				
				
				foreach($tagval as $tagkey_1 => $tagval_1){
					
					if(is_array($tagval_1)){
						
						
					}else{
						
					
						
						
						if(isset($tagtitle[$tagval_1])) $title_1 = $tagtitle[$tagval_1]; else	$title_1 = '';
						
						
						echo '<li> <span class="image_cover"><img class="c2pa_no_metadata" src="data:image/jpeg;base64,'.$binary_data_img[$tagval_1].'" width="'.$size_square.'" height="'.$size_square.'" /></span>  <span class="file_meta_title">'.$title_1.'</span></li>';
						
					}
					
					
				}
				
				echo '</ul>';
				
			}else{
				
							
				
				if(isset($tagtitle[$tagval])) $title_0 = $tagtitle[$tagval]; else	$title_0 = '';
				
				$cl_type = 'ingredients';
				$archive_id = '';
				if(!empty($archive_time_set)){
					$archive_time = str_replace('Z','',$archive_time_set);
					$dateformate_2 = explode("T",$archive_time);
		
					$newDate_2 = '';
		
					if(strpos($archive_time,'T') == false){
						$time_of_sign_2 =  $archive_time;
					}else{
						$newDate_2 = date("M j, Y", strtotime($dateformate_2[0]));
						$newDate_2 .= ' at ';
						$newDate_2 .= date("h:i A", strtotime($dateformate_2[1]));
						$time_of_sign_2 =  $newDate_2;
					}
					$cl_type = 'archive';
					$archive_id = $archive_time_set;
		
				}
				if(!empty($archive_time_set)) $archive_time_set_html = '<span class="timeset">'.$time_of_sign_2.'</span>';  else	$archive_time_set_html = '<span class="file_meta_name">File name</span> <span class="file_meta_title">'.$title_0.'</span>';
				echo '<li>'.$arrow_down.' <span class="image_cover"><span class="c2pa-seal"></span><img class="c2pa_image" data-attach-id="'.$attachment_id.'" data-type="'.$cl_type.'" data-archive-id="'.$archive_id.'" src="'.$path.'" width="'.$size_square.'" height="'.$size_square.'" /></span> '.$archive_time_set_html.' </li>';
				
			}
			
		}
			
		}
		
		echo '</ul>';
		
	
		
	}
function wpclink_find_key_from_value($value = '', $metadata_list = array()){
    
    foreach($metadata_list as $key => $metadata_val){
        if(!is_array($metadata_val)){
            if($metadata_val === (string)$value){
                return $key;
            }
        }
            
    }
    return false;
}
/**
 * CLink Show Metadata Tree Short New
 * 
 * @param string image path
 * @param integer attachment id
 * @param string archive time
 * @param string type
 *
 * @return string html
 */
function wpclink_show_metadata_tree_short_new($path, $attachment_id = 0, $archive_time_set = '', $type = 'ingredients'){
    
$exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
$output = shell_exec(' wget --no-check-certificate --no-cache --no-cookies -qO - "'.add_query_arg( 'cache', uniqid(), $path).'" | '.$exiftool_file.' -G3 -struct -j -b -fast -');
if(empty($output)) return false;
    
$metadata_array = json_decode($output, true);
    
$list = $metadata_array[0];
$active_manifiest_id = wpclink_active_manifest($path);    
// Full
$full_ingredients = array();
// Level 3 and 4
$level_4_array = array();
// Box
$box = array();
    
if($type == 'archive'){
$extra_class = 'archive ';
}else{
$extra_class = 'ing ';
}
foreach ($list as $level_1_key => $level_1_val ){
    // Level 3 and 4
if(preg_match('/Doc1-([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])/', $level_1_key, $output_full_meta)){  
        $level_4_array[$level_1_key] = $level_1_val; 
     
        // Box 
        $box[$output_full_meta[1]] = $output_full_meta[1];
     
    }
    
}
        
$list_full = $level_4_array;
foreach($box as $box_key => $box_value){
   
    $ingredients = array();
    
    $list = wpclink_filter_me($list_full,'1-'.$box_value);
    foreach($list as $key => $list_single){
        if(!is_array($list_single)){
            if(preg_match('/c2pa.claim/', $list_single, $output_array)){
                    $current_doc = explode(':',$key);
                    $current_doc = $current_doc[0];
                    // Current Indgredient 0
                    $ingred_0 = $output_array[0];
                    // Title 
                    if(array_key_exists($current_doc.':Title', $list)){
                        $current_title = $list[$current_doc.':Title'];
                        // Set
                        $ingredients[$ingred_0]['Title'] = $current_title;
                    }
                    // Format 
                    if(array_key_exists($current_doc.':Format', $list)){
                        $current_format = $list[$current_doc.':Format'];
                        // Set
                        $ingredients[$ingred_0]['Format'] = $current_format;
                    }
                    $doc_key = wpclink_find_key_from_value('c2pa.thumbnail.claim.jpeg',$list);
                    if( $doc_key !== false){
                        $doc_key_num = explode(':',$doc_key);
                        if(isset($doc_key_num[0])){
                            $doc_key_num_found = $doc_key_num[0];
                            if(array_key_exists($doc_key_num_found.':C2paThumbnailClaimJpegData', $list)){
                                $doc_key_num_found_data = $doc_key_num_found.':C2paThumbnailClaimJpegData';
                                $ingredients[$ingred_0]['Data'] = $list[$doc_key_num_found_data];
                            }
                            if(array_key_exists($doc_key_num_found.':JUMDLabel', $list)){
                                $doc_key_num_found_label = $doc_key_num_found.':JUMDLabel';
                                $ingredients[$ingred_0]['Label'] = $list[$doc_key_num_found_label];
                            }
                        }
                    }
              }else if(preg_match('/c2pa[[\.\]]ingredient__([0-9]|[1-9][0-9]|[1-9][0-9][0-9])/', $list_single, $output_array)){ 
                    $current_doc = explode(':',$key);
                    $current_doc = $current_doc[0];
                    $current_number = $output_array[1];
                    // Current Indgredient 0
                    $ingred_0 = $output_array[0];
                    // Title 
                    if(array_key_exists($current_doc.':Title', $list)){
                        $current_title = $list[$current_doc.':Title'];
                        // Set
                        $ingredients[$ingred_0]['Title'] = $current_title;
                    }
                    // Format 
                    if(array_key_exists($current_doc.':Format', $list)){
                        $current_format = $list[$current_doc.':Format'];
                        // Set
                        $ingredients[$ingred_0]['Format'] = $current_format;
                    }
                    $doc_key = wpclink_find_key_from_value('c2pa.thumbnail.ingredient__'.$current_number.'.jpeg',$list);
                    if( $doc_key !== false){
                        $doc_key_num = explode(':',$doc_key);
                        if(isset($doc_key_num[0])){
                            $doc_key_num_found = $doc_key_num[0];
                            if(array_key_exists($doc_key_num_found.':C2paThumbnailIngredient_'.$current_number.'JpegData', $list)){
                                $doc_key_num_found_data = $doc_key_num_found.':C2paThumbnailIngredient_'.$current_number.'JpegData';
                                $ingredients[$ingred_0]['Data'] = $list[$doc_key_num_found_data];
                            }
                            if(array_key_exists($doc_key_num_found.':JUMDLabel', $list)){
                                $doc_key_num_found_label = $doc_key_num_found.':JUMDLabel';
                                $ingredients[$ingred_0]['Label'] = $list[$doc_key_num_found_label];
                            }
                        }
                    }
              }else if(preg_match('/c2pa[[\.\]]ingredient/', $list_single, $output_array)){
                    $current_doc = explode(':',$key);
                    $current_doc = $current_doc[0];
                    // Current Indgredient 0
                    $ingred_0 = $output_array[0];
                    // Title 
                    if(array_key_exists($current_doc.':Title', $list)){
                        $current_title = $list[$current_doc.':Title'];
                        // Set
                        $ingredients[$ingred_0]['Title'] = $current_title;
                    }
                    // Format 
                    if(array_key_exists($current_doc.':Format', $list)){
                        $current_format = $list[$current_doc.':Format'];
                        // Set
                        $ingredients[$ingred_0]['Format'] = $current_format;
                    }
                    $doc_key = wpclink_find_key_from_value('c2pa.thumbnail.ingredient.jpeg',$list);
                    if( $doc_key !== false){
                        $doc_key_num = explode(':',$doc_key);
                        if(isset($doc_key_num[0])){
                            $doc_key_num_found = $doc_key_num[0];
                            if(array_key_exists($doc_key_num_found.':C2paThumbnailIngredientJpegData', $list)){
                                $doc_key_num_found_data = $doc_key_num_found.':C2paThumbnailIngredientJpegData';
                                $ingredients[$ingred_0]['Data'] = $list[$doc_key_num_found_data];
                            }
                            if(array_key_exists($doc_key_num_found.':JUMDLabel', $list)){
                                $doc_key_num_found_label = $doc_key_num_found.':JUMDLabel';
                                $ingredients[$ingred_0]['Label'] = $list[$doc_key_num_found_label];
                            }
                        }
                    }
              }
        }
    }
 
    
    $full_ingredients[$box_value] = $ingredients;
    $full_ingredients[$box_value]['box_number'] = $box_value;
    
    }
    
    /*echo '<pre>';
    print_r($full_ingredients);
    echo '</pre>';*/
    
    $last_full_ingredient = end($full_ingredients);
    $prev_full_ingredient = prev($full_ingredients);
    
    
    echo '<ul class="'.$extra_class.'longtree">';
    echo '<li>';
    
    echo '<span class="arrow_down"></span>';
    echo '<span class="image_cover">';
    echo '<span class="c2pa-seal"></span>';
    $ingredient_claim_image_data = $last_full_ingredient['c2pa.claim']['Data'];
    $box_number = $last_full_ingredient['box_number'];
    
    // Claim
    if(isset($ingredient_claim_image_data)){
        echo '<img class="c2pa_image" data-box-num="'.$box_number.'" data-attach-id="'.$attachment_id.'" data-type="'.$type.'" data-archive-id="'.$archive_time_set.'" src="data:image/jpeg;base64,'.str_replace('base64:','',$ingredient_claim_image_data).'"  width="48px" height="48px">';
    }
   
    echo '</span>';
    
     if(isset($last_full_ingredient['c2pa.claim']['Title'])){
        echo '<span class="file_meta_name">File Name</span><span class="file_meta_title">'.$last_full_ingredient['c2pa.claim']['Title'].'</span>';
        
    }
    
    echo '</li>';
        
    
    // Claim    
   echo '<ul class="image_childs">';
    
    
    foreach($last_full_ingredient as $ingredient_block_key => $ingredient_block_val){
        
        
        
        // Not Claim
        if($ingredient_block_key == 'c2pa.claim' || $ingredient_block_key == 'box_number' ){
            // Not above keys
        }else{
            
            echo '<li>'; 
            
            $image_atr = 'class="c2pa_no_metadata"';
            $seal = '';
            
            // Find Ingredient Before Arrow
            if(isset($ingredient_block_val['Title'])){
                if(isset($prev_full_ingredient['c2pa.claim']['Title'])){
                    if($prev_full_ingredient['c2pa.claim']['Title'] == $ingredient_block_val['Title']){
                        
                        $box_number_level_2 = $prev_full_ingredient['box_number'];
                        
                         foreach($prev_full_ingredient as $prev_full_ingredient_key => $prev_full_ingredient_val){
                            if($prev_full_ingredient_key == 'c2pa.claim' || $prev_full_ingredient_key == 'box_number'){
                            }else{
                                  echo '<span class="arrow_down"></span>';
                                  break;
                            }
                        }
                        
                        $seal = '<span class="c2pa-seal"></span>';
                        
                        
                        $image_atr = 'data-attach-id="'.$attachment_id.'" data-box-num="'.$box_number_level_2.'" data-type="'.$type.'" data-archive-id="'.$archive_time_set.'" class="c2pa_image"';
                    }
                }
            }
            
            echo '<span class="image_cover">';
            
            // Image
           if(isset($ingredient_block_val['Data'])){
               echo $seal;
                echo '<img  src="data:image/jpeg;base64,'.str_replace('base64:','',$ingredient_block_val['Data']).'" width="48px" height="48px" '.$image_atr.'></span>';
           }
            // Title
            if(isset($ingredient_block_val['Title'])){
                echo '<span class="file_meta_name">File Name</span><span class="file_meta_title">'.$ingredient_block_val['Title'].'</span>';
            }
            
            // Find Ingredient
            if(isset($ingredient_block_val['Title'])){
                if(isset($prev_full_ingredient['c2pa.claim']['Title'])){
                    if($prev_full_ingredient['c2pa.claim']['Title'] == $ingredient_block_val['Title']){
                        
                
                       
                        foreach($prev_full_ingredient as $prev_full_ingredient_key => $prev_full_ingredient_val){
                            
                            
                            
                            if($prev_full_ingredient_key == 'c2pa.claim' || $prev_full_ingredient_key == 'box_number'){
                                // Not above keys
                            }else{
                                
                                 echo '<ul class="image_childs a1">';
                                
                                    echo '<li> <span class="image_cover">';
            
                                        // Image
                                       if(isset($prev_full_ingredient_val['Data'])){
                                            echo '<img class="c2pa_no_metadata" src="data:image/jpeg;base64,'.str_replace('base64:','',$prev_full_ingredient_val['Data']).'" width="48px" height="48px"></span>';
                                       }
                                        // Title
                                        if(isset($prev_full_ingredient_val['Title'])){
                                            echo '<span class="file_meta_name">File Name</span><span class="file_meta_title">'.$prev_full_ingredient_val['Title'].'</span>';
                                        }
                                
                                    echo '</li>';
                                
                                echo '</ul>';
                                
                            }
                        }
                    }
                }
            }
            
            
            
            
            echo '</li>';
        }
        
    }
    
    echo '</ul>';
    
    echo '</ul>';
    
    echo '<input class="active_manifiest_val '.$extra_class.'" value="'.$active_manifiest_id.'" type="hidden" />';
    
    
    
    
    reset($full_ingredients);
    
}
/**
 * CLink Get Metadata G3
 * 
 * @param string image path
 *
 * @return array metadata
 */
function wpclink_get_metadata_g3($path = ''){
    
    $exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
    $output = shell_exec(' wget --no-check-certificate --no-cache --no-cookies -qO - "'.add_query_arg( 'cache', uniqid(), $path).'" | '.$exiftool_file.' -G3 -struct -j -fast -');
    if(empty($output)) return false;
    $metadata_array = json_decode($output, true);
    $collect = array();
    foreach ($metadata_array[0] as $single_key => $single_val){
        if(!is_array($single_val)){
            //echo $single_val.'<br />';
            if (strpos((string)$single_val, 'c2pa.ingredient') !== false) {
                $getkey = str_replace('JUMDLabel','Title',$single_key);
                $collect[$single_val] = $getkey;
           }
        }
    }
    $collect_2 = array();
    foreach ($collect as $play_key => $play_val){
        $collect_2[$play_key] = $metadata_array[0][$play_val];
    }
    
    if(empty($collect_2)) return false;
    
    return $collect_2;
    
    
}
/**
 * CLink Filter Metadata
 * 
 * @param array metadata
 * @param string filter key
 *
 * @return array metadata
 */
function wpclink_filter_me($metadata_array = array(), $filter_key = ''){
    $array = array();
    foreach ($metadata_array as $key => $value){
        
          if (strpos($key, 'Doc'.$filter_key) !== false) {
              $array[$key] = $value; 
          }
        
        
    }
    return $array;
}
/**
 * CLink JUMP Box Title Printer
 * 
 * @param array metadata
 * @param string filter key
 *
 * @return array metadata
 */
function wpclink_title_printer($title = '', $type = ''){
    if($title == 'c2pa'){
        return strtoupper($title)." Manifest Store ('".$title."')";
    }else if (strpos($type, 'c2ma') !== false) {
        return "Manifest ('c2ma' : '{$title}')";
    }else if(strpos($type, 'c2cs') !== false){
          return "Claim Signature ('c2cs' : '{$title}')";
    }else if(strpos($type, 'c2cl') !== false){
          return "Claim ('c2cl' : '{$title}')";
    }else if(strpos($title, 'c2pa.assertions') !== false){
          return "Assertion Store('c2as' : '{$title}')";
    }else if(strpos($title, 'thumbnail.ingredient') !== false){
          return "Thumbnail Ingredient ('{$title}')";
    }else if(strpos($title, 'c2pa.thumbnail') !== false){
          return "Thumbnail ('{$title}')";
    }else if(strpos($title, 'c2pa.ingredient') !== false){
          return "Ingredient ('cbor' : '{$title}')";
    }else if(strpos($title, 'adobe.credentials') !== false){
          return "Adobe Credentials ('chor' : '{$title}')";
    }else if(strpos($title, 'c2pa.actions') !== false){
          return "Actions ('cbor' : '{$title}')";
    }else if(strpos($title, 'c2pa.hash.data') !== false){
          return "Hash Data ('cbor' : '{$title}')";
    }else if(strpos($title, 'stds.schema-org.CreativeWork') !== false){
          return "Creative Work ('json' : '{$title}')";
    }else if(strpos($title, 'adobe.dictionary') !== false){
          return "Adobe Directory ('cbor' : '{$title}')";
    }else if(strpos($title, 'adobe.credential') !== false){
          return "Adobe Credentials ('cbor' : '{$title}')";
    }else if(strpos($title, 'adobe.beta') !== false){
          return "Adobe Beta ('cbor' : '{$title}')";
    }
    return $title;
}
/**
 * CLink JUMP Box Printer
 * 
 * @param array level 1
 * @param array level 1 output
 * @param string label
 *
 * @return html
 */ 
function wpclink_box_printer($level_1_array = array(), $array_level_1_output = array(), $label = ''){
    
    global $box_labels;
    global $boxes_common;
    $label = '';
        
    if(isset($level_1_array[$array_level_1_output[0].'JUMDType'])){
        
        echo '<span class="level_item"><span class="level_pair_key">JUMDType </span><span class="level_pair_val"> '.$common.$level_1_array[$array_level_1_output[0].'JUMDType'].'</span></span>';
    }
    
    if(isset($level_1_array[$array_level_1_output[0].'JUMDLabel'])){
        
        if(isset($level_1_array[$array_level_1_output[0].'JUMDLabel'])){
            $get_label = $level_1_array[$array_level_1_output[0].'JUMDLabel'];
            
            if($get_label == 'c2pa'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_an_example" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }else if($get_label == 'c2pa.signature'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_digital_signatures" target="_blank"><span class="c2pa_spec" ></span></a></span>';
            }else  if($get_label == 'c2pa.claim'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_claims" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }else  if($get_label == 'c2pa.assertions'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_assertions" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }else  if($get_label == 'c2pa.thumbnail.claim.jpeg'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_thumbnail" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }else  if($get_label == 'c2pa.thumbnail.ingredient.jpeg'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_thumbnails" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }else  if($get_label == 'c2pa.ingredient'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_ingredient" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }else  if($get_label == 'stds.schema-org.CreativeWork'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_creative_work" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }else  if($get_label == 'c2pa.actions'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_actions" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }else  if($get_label == 'c2pa.hash.data'){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_data_hash" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }else  if(strpos($get_label, 'adobe:urn') !== false){
                $link = '<span class="c2pa_spec_box"><a href="https://c2pa.org/specifications/specifications/1.0/specs/C2PA_Specification.html#_manifests" target="_blank"><span class="c2pa_spec"></span></a></span>';
            }
            
        }else{
            $link = '';
        }
        
        
        
        echo '<span class="level_item"><span class="level_pair_key">JUMDLabel'.$link.'</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'JUMDLabel'].'</span></span>';
    }
     if(isset($level_1_array[$array_level_1_output[0].'Title'])){
        echo '<span class="level_item"><span class="level_pair_key">Title</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Title'].'</span></span>';
    }
     if(isset($level_1_array[$array_level_1_output[0].'Format'])){
        echo '<span class="level_item"><span class="level_pair_key">Format</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Format'].'</span></span>';
    }
    if(isset($level_1_array[$array_level_1_output[0].'DocumentID'])){
        echo '<span class="level_item"><span class="level_pair_key">DocumentID</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'DocumentID'].'</span></span>';
    }
     if(isset($level_1_array[$array_level_1_output[0].'InstanceID'])){
        echo '<span class="level_item"><span class="level_pair_key">InstanceID</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'InstanceID'].'</span></span>';
    }
     if(isset($level_1_array[$array_level_1_output[0].'Version'])){
        echo '<span class="level_item"><span class="level_pair_key">Version</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Version'].'</span></span>';
    }
    if(isset($level_1_array[$array_level_1_output[0].'Claim_generator'])){
        echo '<span class="level_item"><span class="level_pair_key">Claim_generator</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Claim_generator'].'</span></span>';
    }   
    if(isset($level_1_array[$array_level_1_output[0].'Signature'])){
        echo '<span class="level_item"><span class="level_pair_key">Signature</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Signature'].'</span></span>';
    }   
    if(isset($level_1_array[$array_level_1_output[0].'Assertions'])){
        
        $unid_id = uniqid();
        
        $value = $level_1_array[$array_level_1_output[0].'Assertions'];
        
        if(is_array($value)){
            
            
            
        $count = 0;
        $count2 = 0;
        $value_count = count($value);
        if($value_count > 1){
            $value_more = ' <span class="level_item"><span class="level_pair_key italic">Assertions</span><span class="level_pair_val"><a data-loader-id="'.$unid_id.'" class="more-loader-btn" href="#">View</a></span></span>';
        }else{
            $value_more = '';
        }
        
        $html .= $value_more;
        
        $html.='<span class="more-loader '.$unid_id.' ">';
            
        $html.='<span class="type_array">';
        foreach($value as $key_a => $single){
            if(isset($single['url'])){
                $count2++;
                if($count2 >= 1){
                    
                    
                    $binary_data = strpos($level_1_array[$array_level_1_output[0].'Assertions']['hash'], 'Binary data');
                    
       
                        if(!empty($single['hash'])){
                            $attr = 'data-key="'.$array_level_1_output[0].'Assertionshash,'.$key_a.'"';
                        }else{
                           $attr = ''; 
                        }
                        $value = $single['hash'];
                        $value = str_replace(array(', use -b option to extract','(',')'),' ',$value);
                        $value = $value.'<a '.$attr.' class="load-binary">View</a><a class="copyme">Copy</a><textarea readonly class="view-binary"></textarea>';
                    
                    
                    
                    
                     $html .= '<span class="level_item"><span class="level_pair_key indent">alg</span><span class="level_pair_val">'.$single['alg'].'</span></span><span class="level_item"><span class="level_pair_key indent">url</span><span class="level_pair_val">'.$single['url'].'</span></span><span class="level_item"><span class="level_pair_key indent">hash</span><span class="level_pair_val">'.$value.'</span></span>';
                }
                
                
            }
        }
            
        $html.='</span></span>';
            
        echo $html;
        
    }else{
        
        echo '<span class="level_item"><span class="level_pair_key italic">Assertions</span> '.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Assertions'].'</span></span>';
            
        }
    }
    
    
    
    
    if(isset($level_1_array[$array_level_1_output[0].'Relationship'])){
        echo '<span class="level_item"><span class="level_pair_key">Relationship</span> '.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Relationship'].'</span></span>';
    }
    
    if(isset($level_1_array[$array_level_1_output[0].'Thumbnail'])){
        
         echo '<span class="level_item"><span class="level_pair_key italic">Thumbnail</span> <span class="level_pair_val"> </span></span>';
        
        echo '<span class="type_array">';
        
          $binary_data = strpos($level_1_array[$array_level_1_output[0].'Thumbnail']['hash'], 'Binary data');
            if($binary_data !== false){
                if(!empty($level_1_array[$array_level_1_output[0].'Thumbnail']['hash'])){
                    $attr = 'data-key="'.$array_level_1_output[0].'Thumbnailhash"';
                }else{
                   $attr = ''; 
                }
                $value = $level_1_array[$array_level_1_output[0].'Thumbnail']['hash'];
                $value = str_replace(array(', use -b option to extract','(',')'),' ',$value);
                $value = $value.'<a '.$attr.' class="load-binary">View</a><a class="copyme">Copy</a><textarea readonly class="view-binary"></textarea>';
            }
        
          if(isset($level_1_array[$array_level_1_output[0].'Thumbnail']['alg'])){
          echo '<span class="level_item"><span class="level_pair_key">alg</span>'.$label. ' <span class="level_pair_val"> '.$level_1_array[$array_level_1_output[0].'Thumbnail']['alg'].'</span></span>';
        }
        
        if(isset($level_1_array[$array_level_1_output[0].'Thumbnail']['url'])){
          echo '<span class="level_item"><span class="level_pair_key">url</span>'.$label. ' <span class="level_pair_val"> '.$level_1_array[$array_level_1_output[0].'Thumbnail']['url'].'</span></span>';
            
        }
                
        echo '<span class="level_item"><span class="level_pair_key">Hash</span>'.$label. ' <span class="level_pair_val"> '.$value.'</span></span>';
        
        echo '</span>';
    }
    
    //Author
    
    
   
    
    if(isset($level_1_array[$array_level_1_output[0].'Item1']['x5chain'])){
        
        
          $binary_data = strpos($level_1_array[$array_level_1_output[0].'Item1']['x5chain'], 'Binary data');
            if($binary_data !== false){
                if(!empty($level_1_array[$array_level_1_output[0].'Item1']['x5chain'])){
                    $attr = 'data-key="'.$array_level_1_output[0].'Item1x5chain"';
                }else{
                   $attr = ''; 
                }
                $value = $level_1_array[$array_level_1_output[0].'Item1']['x5chain'];
                $value = str_replace(array(', use -b option to extract','(',')'),' ',$value);
                $value = $value.'<a '.$attr.' class="load-binary">View</a><a class="copyme">Copy</a><textarea readonly class="view-binary"></textarea>';
            }
        
                
        echo '<span class="level_item"><span class="level_pair_key">x5chain</span>'.$label. ' <span class="level_pair_val">Binary data '.$value.'</span></span>';
    }
    
    
      if(isset($level_1_array[$array_level_1_output[0].'Item1']['temp_signing_time'])){
        
        
           echo '<span class="level_item"><span class="level_pair_key">temp_signing_time</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Item1']['temp_signing_time'].'</span></span>';
    }
    
    if(isset($level_1_array[$array_level_1_output[0].'C2paThumbnailClaimJpegType'])){
        echo '<span class="level_item"><span class="level_pair_key">C2paThumbnailClaimJpegType</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'C2paThumbnailClaimJpegType'].'</span></span>';
    }  
    
    if(isset($level_1_array[$array_level_1_output[0].'Url'])){
        
        
           echo '<span class="level_item"><span class="level_pair_key">Url</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Url'].'</span></span>';
    }
    
      if(isset($level_1_array[$array_level_1_output[0].'Context'])){
        
        
           echo '<span class="level_item"><span class="level_pair_key">Context</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Context'].'</span></span>';
    }
    
    if(isset($level_1_array[$array_level_1_output[0].'Type'])){
        
        
           echo '<span class="level_item"><span class="level_pair_key">Type</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Type'].'</span></span>';
    }
    
    
     if(isset($level_1_array[$array_level_1_output[0].'Author'])){
         
        echo '<span class="level_item"><span class="level_pair_key italic">author</span><span class="level_pair_val"></span></span>';
         
        echo '<span class="type_array">';
        
        $unid_id = uniqid();
        
        $value = $level_1_array[$array_level_1_output[0].'Author'];
        
        if(is_array($value)){
        $count = 0;
        $count2 = 0;
            
        $value_count = count($value);
            
        if($value_count > 1){
            
             foreach($value as $single){
                if($count >= 1) break;
                 
               
                 
                  if(isset($single['@id'])){
                    $html.= '<span class="level_item"><span class="level_pair_key">@id</span><span class="level_pair_val"><br>'.$single['@id'].'</span></span>';
                }
                 
                 $html.= '<span class="level_item"><span class="level_pair_key">@type</span><span class="level_pair_val">'.$single['@type'].'</span></span>';
                 
                 
                 $html.= '<span class="level_item"><span class="level_pair_key">name</span><span class="level_pair_val">'.$single['name'].'</span></span>';
                 
               
                 
               
                 
                 
                 
                  $html.='<span class="level_item"><span class="level_pair_key">identifier</span><span class="level_pair_val">'.$single['identifier'].'</span></span>';
                 
                 if(isset($single['credential'])){
                     
                      $html.='<span class="level_item"><span class="level_pair_key italic">credential</span><span class="level_pair_val"></span></span>';
                    
                     if(is_array($single['credential'])){
                         
                         foreach($single['credential'] as $credential){
                    
                            $html.= '<span class="level_item"><span class="level_pair_key indent">url</span><span class="level_pair_val">'.$credential['url'].'</span></span>';
                             
                         }
                         
                     }
                }
                 
           
                 
                $html.='<span class="level_item"><span class="level_pair_key"></span><span class="level_pair_val"><a data-loader-id="'.$unid_id.'" class="more-loader-btn" href="#">See More +</a></span></span>';
                 
                $count++; 
                 
             }
            
            
            $html.='<span class="more-loader '.$unid_id.' ">';
            
            foreach($value as $single){
                
                if(isset($single['name'])){
                    if($count2 >= 1){
                        
                         if(isset($single['@id'])){
                            
                        $html.= '<span class="level_item"><span class="level_pair_key">@id</span><span class="level_pair_val">'.$single['@id'].'</span></span>';
                            
                        }
                        
                        $html .= '<span class="level_item"><span class="level_pair_key">@type</span><span class="level_pair_val">'.$single['@type'].'</span></span>';
                        
                        $html .= '<span class="level_item"><span class="level_pair_key">name</span><span class="level_pair_val">'.$single['name'].'</span></span>';
                        
                         if(isset($single['credential'])){
                    
                             if(is_array($single['credential'])){
                                 foreach($single['credential'] as $credential){
                                    $html.= '<span class="level_item"><span class="level_pair_key indent">url</span><span class="level_pair_val">'.$credential['url'].'</span></span>';
                                 }
                             }
                        }
                        
                       
                        
                        $html .= '<span class="level_item"><span class="level_pair_key">identifier</span><span class="level_pair_val">'.$single['identifier'].'</span></span><br><br>';
                    }
                    
                    $count2++;
                }
            }
            $html.= '</span>';
            
            
        }else{
            $value_more = '';
        }        
            
        echo $html;
        
    }else{
        
        echo '<span class="level_item"><span class="level_pair_key">Author</span> '.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Author'].'</span></span>';
            
        }
         
         echo '</span>';
    }
    
     if(isset($level_1_array[$array_level_1_output[0].'Verifiable_content_document'])){
         
        echo '<span class="level_item"><span class="level_pair_key italic">Verifiable_content_document</span><span class="level_pair_val"></span></span>';
         
        echo '<span class="type_array">';
        
        $unid_id = uniqid();
        
        $value = $level_1_array[$array_level_1_output[0].'Verifiable_content_document'];
         
    
         
        if(is_array($value)){
  
            
        $value_count = count($value);
            
            // context
            if(is_array($value['@context'])){
                 foreach($value['@context'] as $single){
                        $html.= '<span class="level_item"><span class="level_pair_key">Verifiable_content_document</span><span class="level_pair_val">'.$single.'</span></span>';
                 }
            }
            
            // type
            if(is_array($value['type'])){
                foreach($value['type'] as $key2 => $single){
                        $html.= '<span class="level_item"><span class="level_pair_key italic">type</span><span class="level_pair_val">'.$single.'</span></span>';
                 }
            }
            
            // id
            $html.= '<span class="level_item"><span class="level_pair_key">id</span><span class="level_pair_val">'.$value['id'].'</span></span>';
     
            // credential_subject
            $html.= '<span class="level_item"><span class="level_pair_key italic">credential_subject</span><span class="level_pair_val"></span></span>';
            
            $value['credential_subject'] = array_reverse($value['credential_subject']);
            
            if(is_array($value['credential_subject'])){
                 foreach($value['credential_subject'] as $key => $single){
                        $html.= '<span class="level_item"><span class="level_pair_key indent">'.$key.'</span><span class="level_pair_val">'.$single.'</span></span>';
                 }
            }
            
            
            
            // proof
            $html.= '<span class="level_item"><span class="level_pair_key italic">proof</span><span class="level_pair_val"></span></span>';
            
            if(is_array($value['proof'])){
                
               $value['proof'] = array_reverse($value['proof']);
                foreach($value['proof'] as $key2 => $single){
                        $html.= '<span class="level_item"><span class="level_pair_key indent">'.$key2.'</span><span class="level_pair_val">'.$single.'</span></span>';
                 }
            }
            
            
             
            
            
            
                   
            
        echo $html;
        
    }else{
        
        echo '<span class="level_item"><span class="level_pair_key">Verifiable_content_document</span> '.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Verifiable_content_document'].'</span></span>';
            
        }
         
         echo '</span>';
    }
    
    
    if(isset($level_1_array[$array_level_1_output[0].'Exclusions'])){
         
        echo '<span class="level_item"><span class="level_pair_key italic">Exclusions</span><span class="level_pair_val"></span></span>';
         
        echo '<span class="type_array">';
        
        $unid_id = uniqid();
        
        $value = $level_1_array[$array_level_1_output[0].'Exclusions'];
         
       
         
        if(is_array($value)){
         foreach($value as $key => $single){
                $html.= '<span class="level_item"><span class="level_pair_key">'.$key.'</span><span class="level_pair_val">'.$single.'</span></span>';
         }
        echo $html;
    }else{
        
        echo '<span class="level_item"><span class="level_pair_key">Exclusions</span> <span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Exclusions'].'</span></span>';
            
        }
         
         echo '</span>';
    }
    
    
    if(isset($level_1_array[$array_level_1_output[0].'Actions'])){
         
        echo '<span class="level_item"><span class="level_pair_key italic">Actions</span><span class="level_pair_val"></span></span>';
         
        echo '<span class="type_array">';
        
        $unid_id = uniqid();
        
        $value_actions = $level_1_array[$array_level_1_output[0].'Actions'];
         
       
         
    if(is_array($value_actions)){
         foreach($value_actions as $key_actions => $single_actions){
                echo '<span class="level_item"><span class="level_pair_key">'.$single_actions['action'].'</span><span class="level_pair_val">'.$single_actions['parameters'].'</span></span>';
         }
        
    }else{
        
        echo '<span class="level_item"><span class="level_pair_key">Actions</span> <span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Actions'].'</span></span>';
            
        }
        
        echo '</span>';
         
        
    }
    
    if(isset($level_1_array[$array_level_1_output[0].'Metadata'])){
         
        echo '<span class="level_item"><span class="level_pair_key italic">Metadata</span><span class="level_pair_val"></span></span>';
         
        echo '<span class="type_array">';
        
        $unid_id = uniqid();
        
        $value_metadata = $level_1_array[$array_level_1_output[0].'Metadata'];
         
       
         
        if(is_array($value_metadata)){  
            
         foreach($value_metadata as $value_metadata_key => $value_metadata_single){
             
             if(is_array($value_metadata_single)){
                 
                 echo  '<span class="level_item"><span class="level_pair_key italic">reviewRatings</span><span class="level_pair_val"></span></span>';
                 
                 foreach ($value_metadata_single as $value_metadata_single_key => $value_metadata_single_val ){
                 
                  echo  '<span class="level_item"><span class="level_pair_key indent">explanation</span><span class="level_pair_val">'.$value_metadata_single_val['explanation'].'</span></span>';
                     
                  echo  '<span class="level_item"><span class="level_pair_key indent">code</span><span class="level_pair_val">'.$value_metadata_single_val['code'].'</span></span>';
                     
                 echo  '<span class="level_item"><span class="level_pair_key indent">value</span><span class="level_pair_val">'.$value_metadata_single_val['value'].'</span></span>';
                     
                 }
                 
             }else{
                 
                echo  '<span class="level_item"><span class="level_pair_key ">'.$value_metadata_key.'</span><span class="level_pair_val">'.$value_metadata_single.'</span></span>';
             }
             
             
             
         }
        
        }else{
        
        echo '<span class="level_item"><span class="level_pair_key">Metadata</span> <span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Metadata'].'</span></span>';
            
        }
         
        echo '</span>';
         
    }
    
    
    
     if(isset($level_1_array[$array_level_1_output[0].'C2paThumbnailClaimJpegData'])){
         
        $binary_data = strpos($level_1_array[$array_level_1_output[0].'C2paThumbnailClaimJpegData'], 'Binary data');
            if($binary_data !== false){
                if(!empty($level_1_array[$array_level_1_output[0].'C2paThumbnailClaimJpegData'])){
                    $attr = 'data-key="'.$array_level_1_output[0].'C2paThumbnailClaimJpegData"';
                }else{
                   $attr = ''; 
                }
                $value = $level_1_array[$array_level_1_output[0].'C2paThumbnailClaimJpegData'];
                $value = str_replace(array(', use -b option to extract','(',')','Binary data'),' ',$value);
                $value = $value.'<a '.$attr.' class="load-binary">View</a><a class="copyme">Copy</a><textarea readonly class="view-binary"></textarea>';
            }
        
         
                
        echo '<span class="level_item"><span class="level_pair_key">C2paThumbnailClaimJpegData</span>'.$label. ' <span class="level_pair_val"> Binary data '.$value.'</span></span>';
    }  
    
    if(isset($level_1_array[$array_level_1_output[0].'C2paThumbnailIngredient_1JpegType'])){
        echo '<span class="level_item"><span class="level_pair_key">C2paThumbnailIngredient_1JpegType</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'C2paThumbnailIngredient_1JpegType'].'</span></span>';
    }  
    
     if(isset($level_1_array[$array_level_1_output[0].'C2paThumbnailIngredient_1JpegData'])){
         
        $binary_data = strpos($level_1_array[$array_level_1_output[0].'C2paThumbnailIngredient_1JpegData'], 'Binary data');
            if($binary_data !== false){
                if(!empty($level_1_array[$array_level_1_output[0].'C2paThumbnailIngredient_1JpegData'])){
                    $attr = 'data-key="'.$array_level_1_output[0].'C2paThumbnailIngredient_1JpegData"';
                }else{
                   $attr = ''; 
                }
                $value = $level_1_array[$array_level_1_output[0].'C2paThumbnailIngredient_1JpegData'];
                $value = str_replace(array(', use -b option to extract','(',')','Binary data'),' ',$value);
                $value = $value.'<a '.$attr.' class="load-binary">View</a><a class="copyme">Copy</a><textarea readonly class="view-binary"></textarea>';
            }
        
                
        echo '<span class="level_item"><span class="level_pair_key">C2paThumbnailIngredient_1JpegData</span>'.$label. ' <span class="level_pair_val">Binary data '.$value.'</span></span>';
    }  
    
    if(isset($level_1_array[$array_level_1_output[0].'C2paThumbnailIngredientJpegType'])){
        echo '<span class="level_item"><span class="level_pair_key">C2paThumbnailIngredientJpegType</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'C2paThumbnailIngredientJpegType'].'</span></span>';
    }  
    
    if(isset($level_1_array[$array_level_1_output[0].'Name'])){
        echo '<span class="level_item"><span class="level_pair_key">Name</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Name'].'</span></span>';
    }  
    
     if(isset($level_1_array[$array_level_1_output[0].'Alg'])){
        echo '<span class="level_item"><span class="level_pair_key">Alg</span>'.$label. '<span class="level_pair_val">  '.$level_1_array[$array_level_1_output[0].'Alg'].'</span></span>';
    }  
    
     if(isset($level_1_array[$array_level_1_output[0].'C2paThumbnailIngredientJpegData'])){
         
        $binary_data = strpos($level_1_array[$array_level_1_output[0].'C2paThumbnailIngredientJpegData'], 'Binary data');
            if($binary_data !== false){
                if(!empty($level_1_array[$array_level_1_output[0].'C2paThumbnailIngredientJpegData'])){
                    $attr = 'data-key="'.$array_level_1_output[0].'C2paThumbnailIngredientJpegData"';
                }else{
                   $attr = ''; 
                }
                $value = $level_1_array[$array_level_1_output[0].'C2paThumbnailIngredientJpegData'];
                $value = str_replace(array(', use -b option to extract','(',')','Binary data'),' ',$value);
                $value = $value.'<a '.$attr.' class="load-binary">View</a><a class="copyme">Copy</a><textarea readonly class="view-binary"></textarea>';
            }
        
        echo '<span class="level_item"><span class="level_pair_key">C2paThumbnailIngredientJpegData</span>'.$label. ' <span class="level_pair_val">Binary data '.$value.'</span></span>';
    }  
    
    
     if(isset($level_1_array[$array_level_1_output[0].'Hash'])){
        
        
          $binary_data = strpos($level_1_array[$array_level_1_output[0].'Hash'], 'Binary data');
            if($binary_data !== false){
                if(!empty($level_1_array[$array_level_1_output[0].'Hash'])){
                    $attr = 'data-key="'.$array_level_1_output[0].'Hash"';
                }else{
                   $attr = ''; 
                }
                $value = $level_1_array[$array_level_1_output[0].'Hash'];
                $value = str_replace(array(', use -b option to extract','(',')'),' ',$value);
                $value = $value.'<a '.$attr.' class="load-binary">View</a><a class="copyme">Copy</a><textarea readonly class="view-binary"></textarea>';
            }
        
                
        echo '<span class="level_item"><span class="level_pair_key">Hash</span>'.$label. ' <span class="level_pair_val">Binary data '.$value.'</span></span>';
    }
    
    
     if(isset($level_1_array[$array_level_1_output[0].'Pad'])){
        
        
          $binary_data = strpos($level_1_array[$array_level_1_output[0].'Pad'], 'Binary data');
            if($binary_data !== false){
                if(!empty($level_1_array[$array_level_1_output[0].'Pad'])){
                    $attr = 'data-key="'.$array_level_1_output[0].'Pad"';
                }else{
                   $attr = ''; 
                }
                $value = $level_1_array[$array_level_1_output[0].'Pad'];
                $value = str_replace(array(', use -b option to extract','(',')'),' ',$value);
                $value = $value.'<a '.$attr.' class="load-binary">View</a><a class="copyme">Copy</a><textarea readonly class="view-binary"></textarea>';
            }
        
                
        echo '<span class="level_item"><span class="level_pair_key">Pad</span>'.$label. ' <span class="level_pair_val">Binary data '.$value.'</span></span>';
    }
    
    
    
}
/**
 * CLink Read Metadata G3 Advance
 * 
 * @param string path image
 *
 * @return html
 */ 
function wpclink_read_metadata_g3_advance($path = '', $box_number = ''){
    
$exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
    
$output = shell_exec(' wget --no-check-certificate --no-cache --no-cookies -qO - "'.add_query_arg( 'cache', uniqid(), $path).'" | '.$exiftool_file.' -struct -G3 -j -fast -');
$metadata_array = json_decode($output, true);
    
    
$active_manifiest = wpclink_active_manifest($path);
    
$complete_array =$metadata_array[0];
    
echo '<div class="minifest-boxes2">';
    
foreach ($complete_array as $level_1_key => $level_1_val ){
    
    // Level-1
    if(preg_match('/Doc1[\[^:\]]/', $level_1_key, $output_array)){
        $level_1_array[$level_1_key] = $level_1_val;   
    }
     // Level-2
    if(preg_match('/Doc1-([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[\[^:\]]/', $level_1_key, $output_array)){
        if(!empty($box_number)){
            
            if($active_manifiest == $box_number){
                // All Manifiest for Active Manifiest
                $level_2_array[$level_1_key] = $level_1_val; 
            }else{
                // Ingredient Manifiest
                if($output_array[1] == $box_number){
                     $level_2_array[$level_1_key] = $level_1_val; 
                }
            }
            
            
        }else{
             // All Manifiest for Active Manifiest
             $level_2_array[$level_1_key] = $level_1_val; 
        }
    }
    
    // Level-3
    if(preg_match('/Doc1-([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[\[^:\]]/', $level_1_key, $output_array)){  
        $level_3_array[$level_1_key] = $level_1_val;   
    }
    
    // Level-4
    if(preg_match('/Doc1-([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[\[^:\]]/', $level_1_key, $output_array)){  
        $level_4_array[$level_1_key] = $level_1_val;   
    }
    if(preg_match('/Doc1-([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[\[^:\]]/', $level_1_key, $output_array)){  
        $level_5_array[$level_1_key] = $level_1_val;   
    }
}
    
// Reverse
$level_3_array = array_reverse($level_3_array, true);
foreach($level_1_array as $level_1_play_key => $level_1_play_val){
    
    $level_1_play_key = explode(':',$level_1_play_key);
    $level_1_play_key = str_replace('Doc','',$level_1_play_key[0]);
    // 1
    
    // GUESS
    foreach($level_2_array as $level_2_play_key => $level_2_play_val){
    
    $level_2_play_key = explode(':',$level_2_play_key);
    $level_2_play_key = str_replace('Doc','',$level_2_play_key[0]);
    // 1-1
        
      // echo '<h3>'.$level_2_play_key.'>'.$level_1_play_key.'</h3>';
        
        if (strpos($level_2_play_key, $level_1_play_key) !== false) {
            $level_1_array['Doc'.$level_1_play_key.':array'] = $level_2_array;
            // 1-1=1
            
             foreach($level_3_array as $level_3_play_key => $level_3_play_val){
                 
                    $level_3_play_key = explode(':',$level_3_play_key);
                    $level_3_play_key = str_replace('Doc','',$level_3_play_key[0]);
                 
                   //echo '<h3>'.$level_3_play_key.'>'.$level_2_play_key.'</h3>';
                 
                    if (strpos($level_3_play_key, $level_2_play_key) !== false) {
                                            
                        $level_2_array['Doc'.$level_2_play_key.':array'] = wpclink_filter_me($level_3_array,$level_2_play_key);
                        
                        
                         foreach($level_4_array as $level_4_play_key => $level_4_play_val){
                 
                                $level_4_play_key = explode(':',$level_4_play_key);
                                $level_4_play_key = str_replace('Doc','',$level_4_play_key[0]);
                             
                             // echo '<h3>'.$level_3_play_key.'>'.$level_2_play_key.'</h3>';
                               //echo $level_3_play_key.'<br />';
                                    $level_3_array['Doc'.$level_3_play_key.':array'] = wpclink_filter_me($level_4_array,$level_3_play_key);
                               
                         }
                        
                        
                    }
                 
             }
            
            
            
        }
    
    }
}
 
$level_number = 0;
$level_number_tag = 0;
$level_number_tag2 = 0;
$level_number_tag3 = 0;
$level_number2 = 0;
$level_number3 = 0;
$level_number4 = 0;
    
foreach($level_1_array as $array_level_1_key => $array_level_1_val){
    
   
    
    preg_match('/Doc([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[\[^:\]]/', $array_level_1_key, $array_level_1_output);
    
     if($level_number_tag != $array_level_1_output[1]){
         echo '<fieldset class="level-1">';
    }
    
    
    
    if($level_number != $array_level_1_output[1]){
        $level_number = $array_level_1_output[1];
        
        
       
       
        echo "<legend class='level-1'> ".wpclink_title_printer($level_1_array[$array_level_1_output[0].'JUMDLabel'], $level_1_array[$array_level_1_output[0].'JUMDType'])."</legend>";
        
        wpclink_box_printer($level_1_array,$array_level_1_output,$box_level_1_label);
        
   
        
    }
    
      
    
    // Filter Labels
    $box_level_1_key = explode(':',$array_level_1_key);
    $box_level_1_label = $box_level_1_key[1];
    
    
    if($box_level_1_label != 'array'){
 
    }
    
   
  
        
        // GUESS
        if(array_key_exists($array_level_1_output[0]."array",$level_1_array)){
        
           
            
            $array_level_2 = $level_1_array[$array_level_1_output[0]."array"];
            
           
            
            foreach ($array_level_2 as $array_level_2_key => $array_level_2_val){
                
                preg_match('/Doc([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[\[^:\]]/', $array_level_2_key, $array_level_2_output);
                
                  
                
                 if($level_number2 != $array_level_2_output[2]){
                     
                      // Filter Labels
                    $box_level_2_key = explode(':',$array_level_2_key);
                    $box_level_2_label = $box_level_2_key[1];
                     
                     
                     if($box_level_2_label != 'array'){
                         
                          // Filter Labels
                $box_level_2_key = explode(':',$array_level_2_key);
                $box_level_2_label = $box_level_2_key[1];
                         
                         echo '<fieldset class="level-2">';
                         
                    $level_number2 = $array_level_2_output[2];
                                             
                      echo "<legend  class='level-2'> ".wpclink_title_printer($level_2_array[$array_level_2_output[0].'JUMDLabel'], $level_2_array[$array_level_2_output[0].'JUMDType'])."</legend>";
                     
                     
                     
                     wpclink_box_printer($array_level_2,$array_level_2_output,$box_level_2_label);
                         
                         
                     }
                     
                     
                    
                 }
                
                          
                
                 // Filter Labels
                $box_level_2_key = explode(':',$array_level_2_key);
                $box_level_2_label = $box_level_2_key[1];
                if($box_level_2_label != 'array'){
                }
                
                // GUESS
              
                
        if(array_key_exists($array_level_2_output[0]."array",$array_level_2)){
            //echo 'Key is Exist for >'.$level_number;
            
            
            
            $array_level_3 = $array_level_2[$array_level_2_output[0]."array"];
            
            
            
                     
            
            //print_r($array_level_3);
            
             
            
            
            
            foreach ($array_level_3 as $array_level_3_key => $array_level_3_val){
                
                preg_match('/Doc([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[\[^:\]]/', $array_level_3_key, $array_level_3_output);
                
                 if($level_number_tag3 != $array_level_3_output[3]){
                      if (strpos($array_level_3_key, 'array') === false) {
                          if($level_3_array[$array_level_3_output[0].'JUMDLabel'] == 'c2pa.signature'){
                               $level = '4';
                               echo '<fieldset class="level-'.$level.'">';
                          }else if($level_3_array[$array_level_3_output[0].'JUMDLabel'] == 'c2pa.claim'){
                               $level = '3';
                                echo '<fieldset class="level-'.$level.'">';
                          }else{
                              $level = '5';
                                echo '<fieldset class="level-'.$level.'">';
                          }
                      }
                    }
                
               // echo '<h1>'.$array_level_2_output[2].'</h1>';
                
                 if($level_number3 != $array_level_3_output[3]){
                     
                     // Filter Array Title
                     if (strpos($array_level_3_key, 'array') === false) {
                         
                    $level_number3 = $array_level_3_output[3];
                         
                         
                       
                         echo "<legend class='level-".$level."'> ".wpclink_title_printer($level_3_array[$array_level_3_output[0].'JUMDLabel'], $level_3_array[$array_level_3_output[0].'JUMDType'])."</legend>";
                         
                          wpclink_box_printer($array_level_3,$array_level_3_output,$box_level_3_label);
                         
                     }
                     
                 }
                
                
                // Filter Labels
                $box_level_3_key = explode(':',$array_level_3_key);
                $box_level_3_label = $box_level_3_key[1];
                
                
                
                
                
                
                if($box_level_3_label != 'array'){
                
                }
                
                
                
                           
           
                            if(array_key_exists($array_level_3_output[0]."array",$array_level_3)){
                                
                                
                                $array_level_4 = $array_level_3[$array_level_3_output[0]."array"];
                                
                                foreach ($array_level_4 as $array_level_4_key => $array_level_4_val){
                                    preg_match('/Doc([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[\[^:\]]/', $array_level_4_key, $array_level_4_output);
                                    
                                    
                                    if($level_number_tag4 != $array_level_4_output[4]){
                                      if (strpos($array_level_4_key, 'array') === false) {
                                         echo '<fieldset class="level-6 collapse">';
                                      }
                                    }
                                     if($level_number4 != $array_level_4_output[4]){
                                        $level_number4 = $array_level_4_output[4];
                                         
                                         
                                          echo "<legend class='level-6'> ".wpclink_title_printer($level_4_array[$array_level_4_output[0].'JUMDLabel'], $level_4_array[$array_level_4_output[0].'JUMDType'])."</legend>";
                                         
                                         
                                          wpclink_box_printer($array_level_4,$array_level_4_output,$box_level_4_label);
                                     }
                                    // Filter Labels
                                    $box_level_4_key = explode(':',$array_level_4_key);
                                    $box_level_4_label = $box_level_4_key[1];
                                    
                                    
                                    if($box_level_4_label != 'array'){
                                       
                                    }
                                    
                                     if($level_number_tag4 != $array_level_4_output[4]){
                                     // Filter Array Title
                                         if (strpos($array_level_4_key, 'array') === false) {
                                            echo '</fieldset>';
                                              $level_number_tag4 = $array_level_4_output[4];
                                            
                                         }
                                }
                                }
                                
                                
                                
                                if($level_number_tag3 != $array_level_3_output[3]){
                                     // Filter Array Title
                                         if (strpos($array_level_3_key, 'array') === false) {
                                            echo '</fieldset>';
                                              $level_number_tag3 = $array_level_3_output[3];
                                             
                                         }
                                }
                                
                                // Delete 
                                unset($array_level_3[$array_level_3_output[0]."array"]);
                            }
                    
                    
                }
                
                
                
                
                
    
                
             if($level_number_tag2 != $array_level_2_output[2]){
                   if (strpos($array_level_2_key, 'array') === false) {
                    echo '</fieldset>';
                    $level_number_tag2 = $array_level_2_output[2];
                   }
                }
            
            // Delete 
            unset($array_level_2[$array_level_2_output[0]."array"]);
            
            
            
        }
                 
                 
                
                
                
                
               
                
    
                
            }
            
            if($level_number_tag != $array_level_1_output[1]){
                if (strpos($array_level_1_key, 'array') === false) {
                    echo '</fieldset>';
            $level_number_tag = $array_level_1_output[1];
                }
            }
            // Delete
            unset($level_1_array[$array_level_1_output[0]."array"]);
            
        }
        
       
       
}
    
    
echo '</div>';
}
/**
 * CLink Active Manifest
 * 
 * @param string path image
 *
 * @return string last manifest
 */ 
function wpclink_active_manifest($image_path = ''){
    
       if(empty($image_path)) return 0;
    
	   $exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
        
    
        
        $output = shell_exec('wget --no-check-certificate -qO - "'.add_query_arg( 'cache', uniqid(), $image_path).'" | '.$exiftool_file.' -G3 -struct -j -b -fast -');
    
        if(empty($output)) return false;
    
        $metadata_array = json_decode($output, true);
        
        $list = $metadata_array[0];
        
        foreach ($list as $level_1_key => $level_1_val ){
            // Level 3 and 4
        if(preg_match('/Doc1-([0-9]|[1-9][0-9]|[1-9][0-9][0-9])[[\-\]]([0-9]|[1-9][0-9]|[1-9][0-9][0-9])/', $level_1_key, $output_full_meta)){  
                $level_4_array[$level_1_key] = $level_1_val; 
                // Box 
                $box[$output_full_meta[1]] = $output_full_meta[1];
            }
        }
        $list_full = $level_4_array;
        
        foreach($box as $box_key => $box_value){
            $ingredients = array();
            $list[$box_key] = wpclink_filter_me($list_full,'1-'.$box_value);
        }
    
        return end($box);
    
}
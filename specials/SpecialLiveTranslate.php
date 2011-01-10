<?php

/**
 * Page to manage LiveTranslate translation memories.
 * 
 * @since 0.4
 * 
 * @file SpecialLiveTranslate.php
 * @ingroup LiveTranslate
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SpecialLiveTranslate extends SpecialPage {
	
	/**
	 * Map type numbers to messages.
	 * Messages are build by prepending "livetranslate-tmtype-" and then passing it to wfMsg or similar.
	 * 
	 * @since 0.4
	 * 
	 * @var array
	 */
	protected static $tmTypes = array(
		0 => 'ltf',
		1 => 'tmx',
		2 => 'gcsv',
	);
	
	/**
	 * Constructor.
	 * 
	 * @since 0.4
	 */
	public function __construct() {
		parent::__construct( 'LiveTranslate', 'managetms' );
	}
	
	/**
	 * @see SpecialPage::getDescription
	 * 
	 * @since 0.4
	 */
	public function getDescription() {
		return wfMsg( 'special-' . strtolower( $this->mName ) );
	}
	
	/**
	 * Sets headers - this should be called from the execute() method of all derived classes!
	 * 
	 * @since 0.4
	 */
	public function setHeaders() {
		global $wgOut;
		$wgOut->setArticleRelated( false );
		$wgOut->setRobotPolicy( "noindex,nofollow" );
		$wgOut->setPageTitle( $this->getDescription() );
	}	
	
	/**
	 * Main method.
	 * 
	 * @since 0.4
	 * 
	 * @param string $arg
	 */
	public function execute( $arg ) {
		global $wgOut, $wgUser, $wgRequest, $egPushTargets;
		
		$this->setHeaders();
		$this->outputHeader();
		
		// If the user is authorized, display the page, if not, show an error.
		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return;
		} 
		
		// TODO: handle submissions
		
		$this->displayTMConfig();
	}
	
	/**
	 * Displays the translation memories config table.
	 * 
	 * @since 0.4
	 */		
	protected function displayTMConfig() {
		global $wgOut, $wgUser;
		
		$wgOut->addHtml( Html::openElement(
			'form',
			array(
				'id' => 'tmform',
				'name' => 'tmform',
				'method' => 'post',
				'action' => $this->getTitle()->getLocalURL(),
			)
		) );
		
		$tms = $this->getTMConfigItems();		
		
		if ( count( $tms ) > 0 ) {
			$wgOut->addHTML( Html::openElement(
				'table',
				array( 'class' => 'wikitable', 'style' => 'width:100%' )
			) );

			$wgOut->addHTML( Html::rawElement(
				'tr',
				array(),
				Html::element( 'th', array(), wfMsg( 'livetranslate-special-location' ) ),
				Html::element( 'th', array(), wfMsg( 'livetranslate-special-type' ) )
			) );			
			
			foreach ( $tms as $tm ) {
				$this->displayTMItem( $tm );
			}

			$wgOut->addHTML( Html::closeElement( 'table' ) );
		}
		else {
			$wgOut->addWikiMsg( 'livetranslate-special-no-tms-yet' );
		}
		
		$this->displayAddNewTM();
		
		$wgOut->addHtml(
			Html::input(
				'',
				wfMsg( 'livetranslate-special-button' ),
				'submit',
				array( 'id' => 'tmform-submit' )
			) .
			Html::hidden( 'wpEditToken', $wgUser->editToken() ) .
			Html::closeElement( 'form' )
		);
	}
	
	/**
	 * Displays a single row in the translation memories config table.
	 * 
	 * @since 0.4
	 * 
	 * @return array
	 */		
	protected function getTMConfigItems() {
		$dbr = wfGetDB( DB_SLAVE );
		
		$res = $dbr->select(
			'live_translate_memories',
			array( 'memory_id', 'memory_type', 'memory_location' ),
			array(),
			__METHOD__,
			array( 'LIMIT' => '5000' )
		);
		
		$tms = array();
		
		// Iterate over the result items in the result wrapper to end up with a regular array.
		foreach ( $res as $tm ) {
			$tms[] = $tm;
		}
		
		return $tms;
	}
	
	/**
	 * Displays a single row in the translation memories config table.
	 * 
	 * @since 0.4
	 * 
	 * @param object $tm
	 */	
	protected function displayTMItem( $tm ) {
		global $wgOut;
		
		$wgOut->addHTML( Html::rawElement(
			'tr',
			array(),
			Html::element( 'td', array(), $tm->memory_location ), // TODO
			Html::element( 'rd', array(), $tm->memory_type ) // TODO	
		) );
	}
	
	/**
	 * Displays an input to add a new translation memory.
	 * 
	 * @since 0.4
	 */		
	protected function displayAddNewTM() {
		// TODO
	}
	
}

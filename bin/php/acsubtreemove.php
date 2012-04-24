#!/usr/bin/env php
<?php
/**
 * File containing the mfsubtreemove.php script.
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version  2011.10
 * @package kernel
 */

// Subtree Remove Script
// file  extension/adminmf/bin/php/mfsubtreemove.php

// script initializing
require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "\n" .
                                                         "This script will make a move of a content object subtrees.\n" ),
                                      'use-session' => false,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );
$script->startup();

$scriptOptions = $script->getOptions( "[src-node-id:][dst-node-id:]",
                                      "",
                                      array( 'src-node-id' => "Source subtree parent node ID.",
                                             'dst-node-id' => "Destination node ID."
                                             ),
                                      false,
                                      array( 'user' => true )
                                     );
$script->initialize();

$srcNodeID   = $scriptOptions[ 'src-node-id' ] ? $scriptOptions[ 'src-node-id' ] : false;
$dstNodeID   = $scriptOptions[ 'dst-node-id' ] ? $scriptOptions[ 'dst-node-id' ] : false;



$sourceSubTreeMainNode = ( $srcNodeID ) ? eZContentObjectTreeNode::fetch( $srcNodeID ) : false;
$destinationNode = ( $dstNodeID ) ? eZContentObjectTreeNode::fetch( $dstNodeID ) : false;

if ( !$sourceSubTreeMainNode )
{
    $cli->error( "Subtree move Error! Cannot get subtree main node. Please check src-node-id argument and try again." );
    $script->showHelp();
    $script->shutdown( 1 );
}
if ( !$destinationNode )
{
    $cli->error( "Subtree move Error! Cannot get destination node. Please check dst-node-id argument and try again." );
    $script->showHelp();
    $script->shutdown( 1 );
}

$sourceNodeList = array();

// Also moving hidden nodes
$subTreeParameters = array();
$subTreeParameters['IgnoreVisibility'] = true;
$sourceNodeList = eZContentObjectTreeNode::subTreeByNodeID( $subTreeParameters, $srcNodeID );

$i = 0;
foreach ( $sourceNodeList as $sourceNode )
{
    $nodeToMove = $sourceNode->fetch( $sourceNode->attribute( 'node_id' ) );
    $cli->output( "Moving... ".$nodeToMove->getName() );
    $nodeToMove->move( $dstNodeID );
    $i++;
}

unset( $sourceNodeList );

$cli->output( "\nTotal moved: $i" );
$cli->output( "Done." );
$script->shutdown();

?>
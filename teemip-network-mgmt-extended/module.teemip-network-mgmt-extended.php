<?php
/*
 * @copyright   Copyright (C) 2010-2024 TeemIp
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'teemip-network-mgmt-extended/3.1.3',
	array(
		// Identification
		//
		'label' => 'Network Management Extended',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'teemip-framework/3.1.4',
			'teemip-ip-mgmt/3.1.0',
			'teemip-config-mgmt-adaptor/3.1.0',
			'teemip-network-mgmt/3.1.0',
		),
		'mandatory' => false,
		'visible' => true,
		'installer' => 'NetworkMgmtExtendedInstaller',

		// Components
		//
		'datamodel' => array(
			'model.teemip-network-mgmt-extended.php',
		),
		'data.struct' => array(//'data.struct.IPAudit.xml',
		),
		'data.sample' => array(
			'data/data.sample.InterfaceConnector.xml',
			'data/data.sample.InterfaceSpeed.xml',
			'data/data.sample.Layer2Protocol.xml',
			'data/data.sample.Model.xml',
			'data/data.sample.SnmpCredentials.xml',
			'data/data.sample.ClusterNetwork.xml',
			'data/data.sample.lnkClusterNetworkToIPAddress.xml',
			'data/data.sample.NetworkDevice.xml',
			'data/data.sample.NetworkDeviceComponent.xml',
			'data/data.sample.AggregateLink.xml',
			'data/data.sample.PhysicalInterface.xml',
			'data/data.sample.NetworkDeviceVirtualInterface.xml',
			'data/data.sample.lnkIPInterfaceToIPAddress.xml',
			'data/data.sample.NetworkFlowCapacity.xml',
			'data/data.sample.NetworkFlow.xml',
		),

		// Documentation
		//
		'doc.manual_setup' => '',
		'doc.more_information' => '',

		// Default settings
		//
		'settings' => array(),
	)
);

if (!class_exists('NetworkMgmtExtendedInstaller')) {
	// Module installation handler
	//
	class NetworkMgmtExtendedInstaller extends ModuleInstallerAPI
	{
		public static function BeforeWritingConfig(Config $oConfiguration)
		{
			// If you want to override/force some configuration values, do it here
			return $oConfiguration;
		}

		/**
		 * Handler called before creating or upgrading the database schema
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string PRevious version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 */
		public static function BeforeDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion)
		{
			// If you want to migrate data from one format to another, do it here
			if ($sPreviousVersion == '1.0.0') {
				$sDBSubname = $oConfiguration->Get('db_subname');
				if (CMDBSource::IsField($sDBSubname.'vlan', 'name')) {
					SetupLog::Info("Module teemip-network-mgmt-extended: name column already exists in vlan table -> skip vlan_tag to name migration");
				} else {
					SetupLog::Info("Module teemip-network-mgmt-extended: copy VLAN tags to name and reset them as they become integers only");

					$sSQL1 = "ALTER TABLE ".$sDBSubname."vlan ADD name varchar(255)";
					$sSQL2 = "UPDATE ".$sDBSubname."vlan SET name = vlan_tag";
					$sSQL3 = "UPDATE ".$sDBSubname."vlan SET vlan_tag = 0";
					CMDBSource::Query($sSQL1);
					CMDBSource::Query($sSQL2);
					CMDBSource::Query($sSQL3);

					SetupLog::Info("Module teemip-network-mgmt-extended: VLAN tag migration done");
				}

			}
		}

		/**
		 * Handler called after the creation/update of the database schema
		 *
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string PRevious version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 *
		 * @throws \CoreException
		 * @throws \MySQLException
		 * @throws \MySQLHasGoneAwayException
		 */
		public static function AfterDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion)
		{

		}
	}
}

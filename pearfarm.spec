<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 syntax=php: */

$spec = Pearfarm_PackageSpec::create(array(Pearfarm_PackageSpec::OPT_BASEDIR => dirname(__FILE__)))
             ->setName('ExpiringHash')
             ->setChannel('apinstein.pearfarm.org')
             ->setSummary('Generate tamper-proof, human-readable, hashes for implementing expiring URLs.')
             ->setDescription('Generate tamper-proof, human-readable, hashes for implementing expiring URLs.')
             ->setReleaseVersion('0.0.2')
             ->setReleaseStability('stable')
             ->setApiVersion('0.0.1')
             ->setApiStability('stable')
             ->setLicense(Pearfarm_PackageSpec::LICENSE_MIT)
             ->setNotes('Initial release.')
             ->addMaintainer('lead', 'Alan Pinstein', 'apinstein', 'apinstein@mac.com')
             ->addGitFiles()
             ->addExcludeFiles(array('.gitignore', 'pearfarm.spec'))
             ;

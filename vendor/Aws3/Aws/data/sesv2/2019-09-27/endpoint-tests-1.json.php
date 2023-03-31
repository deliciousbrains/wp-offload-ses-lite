<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3;

// This file was auto-generated from sdk-root/src/data/sesv2/2019-09-27/endpoint-tests-1.json
return ['testCases' => [['documentation' => 'For region ap-south-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-south-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-south-1', 'UseDualStack' => \true]], ['documentation' => 'For region ap-south-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-south-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-south-1', 'UseDualStack' => \false]], ['documentation' => 'For region ap-south-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-south-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-south-1', 'UseDualStack' => \true]], ['documentation' => 'For region ap-south-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-south-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-south-1', 'UseDualStack' => \false]], ['documentation' => 'For region eu-south-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-south-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-south-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-south-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-south-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-south-1', 'UseDualStack' => \false]], ['documentation' => 'For region eu-south-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-south-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-south-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-south-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-south-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-south-1', 'UseDualStack' => \false]], ['documentation' => 'For region ca-central-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ca-central-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'ca-central-1', 'UseDualStack' => \true]], ['documentation' => 'For region ca-central-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ca-central-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'ca-central-1', 'UseDualStack' => \false]], ['documentation' => 'For region ca-central-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.ca-central-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'ca-central-1', 'UseDualStack' => \true]], ['documentation' => 'For region ca-central-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.ca-central-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'ca-central-1', 'UseDualStack' => \false]], ['documentation' => 'For region eu-central-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-central-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-central-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-central-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-central-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-central-1', 'UseDualStack' => \false]], ['documentation' => 'For region eu-central-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-central-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-central-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-central-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-central-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-central-1', 'UseDualStack' => \false]], ['documentation' => 'For region us-west-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-west-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-west-1', 'UseDualStack' => \true]], ['documentation' => 'For region us-west-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-west-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-west-1', 'UseDualStack' => \false]], ['documentation' => 'For region us-west-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-west-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-west-1', 'UseDualStack' => \true]], ['documentation' => 'For region us-west-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-west-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-west-1', 'UseDualStack' => \false]], ['documentation' => 'For region us-west-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-west-2.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-west-2', 'UseDualStack' => \true]], ['documentation' => 'For region us-west-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-west-2.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-west-2', 'UseDualStack' => \false]], ['documentation' => 'For region us-west-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-west-2.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-west-2', 'UseDualStack' => \true]], ['documentation' => 'For region us-west-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-west-2.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-west-2', 'UseDualStack' => \false]], ['documentation' => 'For region af-south-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.af-south-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'af-south-1', 'UseDualStack' => \true]], ['documentation' => 'For region af-south-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.af-south-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'af-south-1', 'UseDualStack' => \false]], ['documentation' => 'For region af-south-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.af-south-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'af-south-1', 'UseDualStack' => \true]], ['documentation' => 'For region af-south-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.af-south-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'af-south-1', 'UseDualStack' => \false]], ['documentation' => 'For region eu-north-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-north-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-north-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-north-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-north-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-north-1', 'UseDualStack' => \false]], ['documentation' => 'For region eu-north-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-north-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-north-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-north-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-north-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-north-1', 'UseDualStack' => \false]], ['documentation' => 'For region eu-west-3 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-west-3.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-west-3', 'UseDualStack' => \true]], ['documentation' => 'For region eu-west-3 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-west-3.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-west-3', 'UseDualStack' => \false]], ['documentation' => 'For region eu-west-3 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-west-3.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-west-3', 'UseDualStack' => \true]], ['documentation' => 'For region eu-west-3 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-west-3.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-west-3', 'UseDualStack' => \false]], ['documentation' => 'For region eu-west-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-west-2.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-west-2', 'UseDualStack' => \true]], ['documentation' => 'For region eu-west-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-west-2.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-west-2', 'UseDualStack' => \false]], ['documentation' => 'For region eu-west-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-west-2.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-west-2', 'UseDualStack' => \true]], ['documentation' => 'For region eu-west-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-west-2.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-west-2', 'UseDualStack' => \false]], ['documentation' => 'For region eu-west-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-west-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-west-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-west-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.eu-west-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-west-1', 'UseDualStack' => \false]], ['documentation' => 'For region eu-west-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-west-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-west-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-west-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.eu-west-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-west-1', 'UseDualStack' => \false]], ['documentation' => 'For region ap-northeast-3 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-northeast-3.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-northeast-3', 'UseDualStack' => \true]], ['documentation' => 'For region ap-northeast-3 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-northeast-3.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-northeast-3', 'UseDualStack' => \false]], ['documentation' => 'For region ap-northeast-3 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-northeast-3.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-northeast-3', 'UseDualStack' => \true]], ['documentation' => 'For region ap-northeast-3 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-northeast-3.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-northeast-3', 'UseDualStack' => \false]], ['documentation' => 'For region ap-northeast-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-northeast-2.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-northeast-2', 'UseDualStack' => \true]], ['documentation' => 'For region ap-northeast-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-northeast-2.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-northeast-2', 'UseDualStack' => \false]], ['documentation' => 'For region ap-northeast-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-northeast-2.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-northeast-2', 'UseDualStack' => \true]], ['documentation' => 'For region ap-northeast-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-northeast-2.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-northeast-2', 'UseDualStack' => \false]], ['documentation' => 'For region ap-northeast-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-northeast-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-northeast-1', 'UseDualStack' => \true]], ['documentation' => 'For region ap-northeast-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-northeast-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-northeast-1', 'UseDualStack' => \false]], ['documentation' => 'For region ap-northeast-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-northeast-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-northeast-1', 'UseDualStack' => \true]], ['documentation' => 'For region ap-northeast-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-northeast-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-northeast-1', 'UseDualStack' => \false]], ['documentation' => 'For region me-south-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.me-south-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'me-south-1', 'UseDualStack' => \true]], ['documentation' => 'For region me-south-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.me-south-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'me-south-1', 'UseDualStack' => \false]], ['documentation' => 'For region me-south-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.me-south-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'me-south-1', 'UseDualStack' => \true]], ['documentation' => 'For region me-south-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.me-south-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'me-south-1', 'UseDualStack' => \false]], ['documentation' => 'For region sa-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.sa-east-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'sa-east-1', 'UseDualStack' => \true]], ['documentation' => 'For region sa-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.sa-east-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'sa-east-1', 'UseDualStack' => \false]], ['documentation' => 'For region sa-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.sa-east-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'sa-east-1', 'UseDualStack' => \true]], ['documentation' => 'For region sa-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.sa-east-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'sa-east-1', 'UseDualStack' => \false]], ['documentation' => 'For region us-gov-west-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-gov-west-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-gov-west-1', 'UseDualStack' => \true]], ['documentation' => 'For region us-gov-west-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-gov-west-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-gov-west-1', 'UseDualStack' => \false]], ['documentation' => 'For region us-gov-west-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-gov-west-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-gov-west-1', 'UseDualStack' => \true]], ['documentation' => 'For region us-gov-west-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-gov-west-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-gov-west-1', 'UseDualStack' => \false]], ['documentation' => 'For region ap-southeast-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-southeast-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-southeast-1', 'UseDualStack' => \true]], ['documentation' => 'For region ap-southeast-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-southeast-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-southeast-1', 'UseDualStack' => \false]], ['documentation' => 'For region ap-southeast-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-southeast-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-southeast-1', 'UseDualStack' => \true]], ['documentation' => 'For region ap-southeast-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-southeast-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-southeast-1', 'UseDualStack' => \false]], ['documentation' => 'For region ap-southeast-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-southeast-2.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-southeast-2', 'UseDualStack' => \true]], ['documentation' => 'For region ap-southeast-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-southeast-2.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-southeast-2', 'UseDualStack' => \false]], ['documentation' => 'For region ap-southeast-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-southeast-2.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-southeast-2', 'UseDualStack' => \true]], ['documentation' => 'For region ap-southeast-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-southeast-2.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-southeast-2', 'UseDualStack' => \false]], ['documentation' => 'For region ap-southeast-3 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-southeast-3.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-southeast-3', 'UseDualStack' => \true]], ['documentation' => 'For region ap-southeast-3 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.ap-southeast-3.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-southeast-3', 'UseDualStack' => \false]], ['documentation' => 'For region ap-southeast-3 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-southeast-3.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-southeast-3', 'UseDualStack' => \true]], ['documentation' => 'For region ap-southeast-3 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.ap-southeast-3.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-southeast-3', 'UseDualStack' => \false]], ['documentation' => 'For region us-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-east-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-east-1', 'UseDualStack' => \true]], ['documentation' => 'For region us-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-east-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-east-1', 'UseDualStack' => \false]], ['documentation' => 'For region us-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-east-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \true]], ['documentation' => 'For region us-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-east-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \false]], ['documentation' => 'For region us-east-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-east-2.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-east-2', 'UseDualStack' => \true]], ['documentation' => 'For region us-east-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.us-east-2.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-east-2', 'UseDualStack' => \false]], ['documentation' => 'For region us-east-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-east-2.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-2', 'UseDualStack' => \true]], ['documentation' => 'For region us-east-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.us-east-2.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-2', 'UseDualStack' => \false]], ['documentation' => 'For region cn-northwest-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.cn-northwest-1.api.amazonwebservices.com.cn']], 'params' => ['UseFIPS' => \true, 'Region' => 'cn-northwest-1', 'UseDualStack' => \true]], ['documentation' => 'For region cn-northwest-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email-fips.cn-northwest-1.amazonaws.com.cn']], 'params' => ['UseFIPS' => \true, 'Region' => 'cn-northwest-1', 'UseDualStack' => \false]], ['documentation' => 'For region cn-northwest-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://email.cn-northwest-1.api.amazonwebservices.com.cn']], 'params' => ['UseFIPS' => \false, 'Region' => 'cn-northwest-1', 'UseDualStack' => \true]], ['documentation' => 'For region cn-northwest-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://email.cn-northwest-1.amazonaws.com.cn']], 'params' => ['UseFIPS' => \false, 'Region' => 'cn-northwest-1', 'UseDualStack' => \false]], ['documentation' => 'For custom endpoint with fips disabled and dualstack disabled', 'expect' => ['endpoint' => ['url' => 'https://example.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \false, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips enabled and dualstack disabled', 'expect' => ['error' => 'Invalid Configuration: FIPS and custom endpoint are not supported'], 'params' => ['UseFIPS' => \true, 'Region' => 'us-east-1', 'UseDualStack' => \false, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips disabled and dualstack enabled', 'expect' => ['error' => 'Invalid Configuration: Dualstack and custom endpoint are not supported'], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \true, 'Endpoint' => 'https://example.com']]], 'version' => '1.0'];
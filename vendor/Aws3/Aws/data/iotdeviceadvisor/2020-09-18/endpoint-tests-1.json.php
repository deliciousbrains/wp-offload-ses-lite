<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3;

// This file was auto-generated from sdk-root/src/data/iotdeviceadvisor/2020-09-18/endpoint-tests-1.json
return ['testCases' => [['documentation' => 'For region us-west-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor-fips.us-west-2.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-west-2', 'UseDualStack' => \true]], ['documentation' => 'For region us-west-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor-fips.us-west-2.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-west-2', 'UseDualStack' => \false]], ['documentation' => 'For region us-west-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor.us-west-2.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-west-2', 'UseDualStack' => \true]], ['documentation' => 'For region us-west-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor.us-west-2.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-west-2', 'UseDualStack' => \false]], ['documentation' => 'For region eu-west-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor-fips.eu-west-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-west-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-west-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor-fips.eu-west-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'eu-west-1', 'UseDualStack' => \false]], ['documentation' => 'For region eu-west-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor.eu-west-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-west-1', 'UseDualStack' => \true]], ['documentation' => 'For region eu-west-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor.eu-west-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'eu-west-1', 'UseDualStack' => \false]], ['documentation' => 'For region ap-northeast-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor-fips.ap-northeast-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-northeast-1', 'UseDualStack' => \true]], ['documentation' => 'For region ap-northeast-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor-fips.ap-northeast-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'ap-northeast-1', 'UseDualStack' => \false]], ['documentation' => 'For region ap-northeast-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor.ap-northeast-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-northeast-1', 'UseDualStack' => \true]], ['documentation' => 'For region ap-northeast-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor.ap-northeast-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'ap-northeast-1', 'UseDualStack' => \false]], ['documentation' => 'For region us-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor-fips.us-east-1.api.aws']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-east-1', 'UseDualStack' => \true]], ['documentation' => 'For region us-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor-fips.us-east-1.amazonaws.com']], 'params' => ['UseFIPS' => \true, 'Region' => 'us-east-1', 'UseDualStack' => \false]], ['documentation' => 'For region us-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor.us-east-1.api.aws']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \true]], ['documentation' => 'For region us-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://api.iotdeviceadvisor.us-east-1.amazonaws.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \false]], ['documentation' => 'For custom endpoint with fips disabled and dualstack disabled', 'expect' => ['endpoint' => ['url' => 'https://example.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \false, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips enabled and dualstack disabled', 'expect' => ['error' => 'Invalid Configuration: FIPS and custom endpoint are not supported'], 'params' => ['UseFIPS' => \true, 'Region' => 'us-east-1', 'UseDualStack' => \false, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips disabled and dualstack enabled', 'expect' => ['error' => 'Invalid Configuration: Dualstack and custom endpoint are not supported'], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \true, 'Endpoint' => 'https://example.com']]], 'version' => '1.0'];

<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3;

// This file was auto-generated from sdk-root/src/data/kinesis-video-media/2017-09-30/endpoint-tests-1.json
return ['testCases' => [['documentation' => 'For region ap-south-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-south-1.api.aws']], 'params' => ['Region' => 'ap-south-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region ap-south-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-south-1.amazonaws.com']], 'params' => ['Region' => 'ap-south-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region ap-south-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-south-1.api.aws']], 'params' => ['Region' => 'ap-south-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region ap-south-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-south-1.amazonaws.com']], 'params' => ['Region' => 'ap-south-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-gov-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-gov-east-1.api.aws']], 'params' => ['Region' => 'us-gov-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-gov-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-gov-east-1.amazonaws.com']], 'params' => ['Region' => 'us-gov-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-gov-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-gov-east-1.api.aws']], 'params' => ['Region' => 'us-gov-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-gov-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-gov-east-1.amazonaws.com']], 'params' => ['Region' => 'us-gov-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region ca-central-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ca-central-1.api.aws']], 'params' => ['Region' => 'ca-central-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region ca-central-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ca-central-1.amazonaws.com']], 'params' => ['Region' => 'ca-central-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region ca-central-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ca-central-1.api.aws']], 'params' => ['Region' => 'ca-central-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region ca-central-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ca-central-1.amazonaws.com']], 'params' => ['Region' => 'ca-central-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region eu-central-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-central-1.api.aws']], 'params' => ['Region' => 'eu-central-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region eu-central-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-central-1.amazonaws.com']], 'params' => ['Region' => 'eu-central-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region eu-central-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-central-1.api.aws']], 'params' => ['Region' => 'eu-central-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region eu-central-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-central-1.amazonaws.com']], 'params' => ['Region' => 'eu-central-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-iso-west-1 with FIPS enabled and DualStack enabled', 'expect' => ['error' => 'FIPS and DualStack are enabled, but this partition does not support one or both'], 'params' => ['Region' => 'us-iso-west-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-iso-west-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-iso-west-1.c2s.ic.gov']], 'params' => ['Region' => 'us-iso-west-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-iso-west-1 with FIPS disabled and DualStack enabled', 'expect' => ['error' => 'DualStack is enabled but this partition does not support DualStack'], 'params' => ['Region' => 'us-iso-west-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-iso-west-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-iso-west-1.c2s.ic.gov']], 'params' => ['Region' => 'us-iso-west-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-west-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-west-2.api.aws']], 'params' => ['Region' => 'us-west-2', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-west-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-west-2.amazonaws.com']], 'params' => ['Region' => 'us-west-2', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-west-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-west-2.api.aws']], 'params' => ['Region' => 'us-west-2', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-west-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-west-2.amazonaws.com']], 'params' => ['Region' => 'us-west-2', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region af-south-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.af-south-1.api.aws']], 'params' => ['Region' => 'af-south-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region af-south-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.af-south-1.amazonaws.com']], 'params' => ['Region' => 'af-south-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region af-south-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.af-south-1.api.aws']], 'params' => ['Region' => 'af-south-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region af-south-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.af-south-1.amazonaws.com']], 'params' => ['Region' => 'af-south-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region eu-north-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-north-1.api.aws']], 'params' => ['Region' => 'eu-north-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region eu-north-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-north-1.amazonaws.com']], 'params' => ['Region' => 'eu-north-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region eu-north-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-north-1.api.aws']], 'params' => ['Region' => 'eu-north-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region eu-north-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-north-1.amazonaws.com']], 'params' => ['Region' => 'eu-north-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region eu-west-3 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-west-3.api.aws']], 'params' => ['Region' => 'eu-west-3', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region eu-west-3 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-west-3.amazonaws.com']], 'params' => ['Region' => 'eu-west-3', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region eu-west-3 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-west-3.api.aws']], 'params' => ['Region' => 'eu-west-3', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region eu-west-3 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-west-3.amazonaws.com']], 'params' => ['Region' => 'eu-west-3', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region eu-west-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-west-2.api.aws']], 'params' => ['Region' => 'eu-west-2', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region eu-west-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-west-2.amazonaws.com']], 'params' => ['Region' => 'eu-west-2', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region eu-west-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-west-2.api.aws']], 'params' => ['Region' => 'eu-west-2', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region eu-west-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-west-2.amazonaws.com']], 'params' => ['Region' => 'eu-west-2', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region eu-west-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-west-1.api.aws']], 'params' => ['Region' => 'eu-west-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region eu-west-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.eu-west-1.amazonaws.com']], 'params' => ['Region' => 'eu-west-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region eu-west-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-west-1.api.aws']], 'params' => ['Region' => 'eu-west-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region eu-west-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.eu-west-1.amazonaws.com']], 'params' => ['Region' => 'eu-west-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region ap-northeast-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-northeast-2.api.aws']], 'params' => ['Region' => 'ap-northeast-2', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region ap-northeast-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-northeast-2.amazonaws.com']], 'params' => ['Region' => 'ap-northeast-2', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region ap-northeast-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-northeast-2.api.aws']], 'params' => ['Region' => 'ap-northeast-2', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region ap-northeast-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-northeast-2.amazonaws.com']], 'params' => ['Region' => 'ap-northeast-2', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region ap-northeast-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-northeast-1.api.aws']], 'params' => ['Region' => 'ap-northeast-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region ap-northeast-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-northeast-1.amazonaws.com']], 'params' => ['Region' => 'ap-northeast-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region ap-northeast-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-northeast-1.api.aws']], 'params' => ['Region' => 'ap-northeast-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region ap-northeast-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-northeast-1.amazonaws.com']], 'params' => ['Region' => 'ap-northeast-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region me-south-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.me-south-1.api.aws']], 'params' => ['Region' => 'me-south-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region me-south-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.me-south-1.amazonaws.com']], 'params' => ['Region' => 'me-south-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region me-south-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.me-south-1.api.aws']], 'params' => ['Region' => 'me-south-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region me-south-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.me-south-1.amazonaws.com']], 'params' => ['Region' => 'me-south-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region sa-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.sa-east-1.api.aws']], 'params' => ['Region' => 'sa-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region sa-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.sa-east-1.amazonaws.com']], 'params' => ['Region' => 'sa-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region sa-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.sa-east-1.api.aws']], 'params' => ['Region' => 'sa-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region sa-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.sa-east-1.amazonaws.com']], 'params' => ['Region' => 'sa-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region ap-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-east-1.api.aws']], 'params' => ['Region' => 'ap-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region ap-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-east-1.amazonaws.com']], 'params' => ['Region' => 'ap-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region ap-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-east-1.api.aws']], 'params' => ['Region' => 'ap-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region ap-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-east-1.amazonaws.com']], 'params' => ['Region' => 'ap-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region cn-north-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.cn-north-1.api.amazonwebservices.com.cn']], 'params' => ['Region' => 'cn-north-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region cn-north-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.cn-north-1.amazonaws.com.cn']], 'params' => ['Region' => 'cn-north-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region cn-north-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.cn-north-1.api.amazonwebservices.com.cn']], 'params' => ['Region' => 'cn-north-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region cn-north-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.cn-north-1.amazonaws.com.cn']], 'params' => ['Region' => 'cn-north-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-gov-west-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-gov-west-1.api.aws']], 'params' => ['Region' => 'us-gov-west-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-gov-west-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-gov-west-1.amazonaws.com']], 'params' => ['Region' => 'us-gov-west-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-gov-west-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-gov-west-1.api.aws']], 'params' => ['Region' => 'us-gov-west-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-gov-west-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-gov-west-1.amazonaws.com']], 'params' => ['Region' => 'us-gov-west-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region ap-southeast-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-southeast-1.api.aws']], 'params' => ['Region' => 'ap-southeast-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region ap-southeast-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-southeast-1.amazonaws.com']], 'params' => ['Region' => 'ap-southeast-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region ap-southeast-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-southeast-1.api.aws']], 'params' => ['Region' => 'ap-southeast-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region ap-southeast-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-southeast-1.amazonaws.com']], 'params' => ['Region' => 'ap-southeast-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region ap-southeast-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-southeast-2.api.aws']], 'params' => ['Region' => 'ap-southeast-2', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region ap-southeast-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.ap-southeast-2.amazonaws.com']], 'params' => ['Region' => 'ap-southeast-2', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region ap-southeast-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-southeast-2.api.aws']], 'params' => ['Region' => 'ap-southeast-2', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region ap-southeast-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.ap-southeast-2.amazonaws.com']], 'params' => ['Region' => 'ap-southeast-2', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-iso-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['error' => 'FIPS and DualStack are enabled, but this partition does not support one or both'], 'params' => ['Region' => 'us-iso-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-iso-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-iso-east-1.c2s.ic.gov']], 'params' => ['Region' => 'us-iso-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-iso-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['error' => 'DualStack is enabled but this partition does not support DualStack'], 'params' => ['Region' => 'us-iso-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-iso-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-iso-east-1.c2s.ic.gov']], 'params' => ['Region' => 'us-iso-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-east-1.api.aws']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-east-1.amazonaws.com']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-east-1.api.aws']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-east-1.amazonaws.com']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-east-2 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-east-2.api.aws']], 'params' => ['Region' => 'us-east-2', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-east-2 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-east-2.amazonaws.com']], 'params' => ['Region' => 'us-east-2', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-east-2 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-east-2.api.aws']], 'params' => ['Region' => 'us-east-2', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-east-2 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-east-2.amazonaws.com']], 'params' => ['Region' => 'us-east-2', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-isob-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['error' => 'FIPS and DualStack are enabled, but this partition does not support one or both'], 'params' => ['Region' => 'us-isob-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-isob-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo-fips.us-isob-east-1.sc2s.sgov.gov']], 'params' => ['Region' => 'us-isob-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-isob-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['error' => 'DualStack is enabled but this partition does not support DualStack'], 'params' => ['Region' => 'us-isob-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-isob-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://kinesisvideo.us-isob-east-1.sc2s.sgov.gov']], 'params' => ['Region' => 'us-isob-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For custom endpoint with fips disabled and dualstack disabled', 'expect' => ['endpoint' => ['url' => 'https://example.com']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips enabled and dualstack disabled', 'expect' => ['error' => 'Invalid Configuration: FIPS and custom endpoint are not supported'], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips disabled and dualstack enabled', 'expect' => ['error' => 'Invalid Configuration: Dualstack and custom endpoint are not supported'], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false, 'Endpoint' => 'https://example.com']]], 'version' => '1.0'];
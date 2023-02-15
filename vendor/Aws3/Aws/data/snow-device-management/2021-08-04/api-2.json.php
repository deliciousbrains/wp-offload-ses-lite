<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3;

// This file was auto-generated from sdk-root/src/data/snow-device-management/2021-08-04/api-2.json
return ['version' => '2.0', 'metadata' => ['apiVersion' => '2021-08-04', 'endpointPrefix' => 'snow-device-management', 'jsonVersion' => '1.1', 'protocol' => 'rest-json', 'serviceFullName' => 'AWS Snow Device Management', 'serviceId' => 'Snow Device Management', 'signatureVersion' => 'v4', 'signingName' => 'snow-device-management', 'uid' => 'snow-device-management-2021-08-04'], 'operations' => ['CancelTask' => ['name' => 'CancelTask', 'http' => ['method' => 'POST', 'requestUri' => '/task/{taskId}/cancel', 'responseCode' => 200], 'input' => ['shape' => 'CancelTaskInput'], 'output' => ['shape' => 'CancelTaskOutput'], 'errors' => [['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'CreateTask' => ['name' => 'CreateTask', 'http' => ['method' => 'POST', 'requestUri' => '/task', 'responseCode' => 200], 'input' => ['shape' => 'CreateTaskInput'], 'output' => ['shape' => 'CreateTaskOutput'], 'errors' => [['shape' => 'ServiceQuotaExceededException'], ['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'DescribeDevice' => ['name' => 'DescribeDevice', 'http' => ['method' => 'POST', 'requestUri' => '/managed-device/{managedDeviceId}/describe', 'responseCode' => 200], 'input' => ['shape' => 'DescribeDeviceInput'], 'output' => ['shape' => 'DescribeDeviceOutput'], 'errors' => [['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'DescribeDeviceEc2Instances' => ['name' => 'DescribeDeviceEc2Instances', 'http' => ['method' => 'POST', 'requestUri' => '/managed-device/{managedDeviceId}/resources/ec2/describe', 'responseCode' => 200], 'input' => ['shape' => 'DescribeDeviceEc2Input'], 'output' => ['shape' => 'DescribeDeviceEc2Output'], 'errors' => [['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'DescribeExecution' => ['name' => 'DescribeExecution', 'http' => ['method' => 'POST', 'requestUri' => '/task/{taskId}/execution/{managedDeviceId}', 'responseCode' => 200], 'input' => ['shape' => 'DescribeExecutionInput'], 'output' => ['shape' => 'DescribeExecutionOutput'], 'errors' => [['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'DescribeTask' => ['name' => 'DescribeTask', 'http' => ['method' => 'POST', 'requestUri' => '/task/{taskId}', 'responseCode' => 200], 'input' => ['shape' => 'DescribeTaskInput'], 'output' => ['shape' => 'DescribeTaskOutput'], 'errors' => [['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'ListDeviceResources' => ['name' => 'ListDeviceResources', 'http' => ['method' => 'GET', 'requestUri' => '/managed-device/{managedDeviceId}/resources', 'responseCode' => 200], 'input' => ['shape' => 'ListDeviceResourcesInput'], 'output' => ['shape' => 'ListDeviceResourcesOutput'], 'errors' => [['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'ListDevices' => ['name' => 'ListDevices', 'http' => ['method' => 'GET', 'requestUri' => '/managed-devices', 'responseCode' => 200], 'input' => ['shape' => 'ListDevicesInput'], 'output' => ['shape' => 'ListDevicesOutput'], 'errors' => [['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'ListExecutions' => ['name' => 'ListExecutions', 'http' => ['method' => 'GET', 'requestUri' => '/executions', 'responseCode' => 200], 'input' => ['shape' => 'ListExecutionsInput'], 'output' => ['shape' => 'ListExecutionsOutput'], 'errors' => [['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'ListTagsForResource' => ['name' => 'ListTagsForResource', 'http' => ['method' => 'GET', 'requestUri' => '/tags/{resourceArn}', 'responseCode' => 200], 'input' => ['shape' => 'ListTagsForResourceInput'], 'output' => ['shape' => 'ListTagsForResourceOutput'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException']]], 'ListTasks' => ['name' => 'ListTasks', 'http' => ['method' => 'GET', 'requestUri' => '/tasks', 'responseCode' => 200], 'input' => ['shape' => 'ListTasksInput'], 'output' => ['shape' => 'ListTasksOutput'], 'errors' => [['shape' => 'ThrottlingException'], ['shape' => 'InternalServerException'], ['shape' => 'ValidationException'], ['shape' => 'AccessDeniedException']]], 'TagResource' => ['name' => 'TagResource', 'http' => ['method' => 'POST', 'requestUri' => '/tags/{resourceArn}', 'responseCode' => 200], 'input' => ['shape' => 'TagResourceInput'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException']]], 'UntagResource' => ['name' => 'UntagResource', 'http' => ['method' => 'DELETE', 'requestUri' => '/tags/{resourceArn}', 'responseCode' => 200], 'input' => ['shape' => 'UntagResourceInput'], 'errors' => [['shape' => 'InternalServerException'], ['shape' => 'ResourceNotFoundException'], ['shape' => 'ValidationException']], 'idempotent' => \true]], 'shapes' => ['AccessDeniedException' => ['type' => 'structure', 'required' => ['message'], 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 403, 'senderFault' => \true], 'exception' => \true], 'AttachmentStatus' => ['type' => 'string', 'enum' => ['ATTACHING', 'ATTACHED', 'DETACHING', 'DETACHED']], 'Boolean' => ['type' => 'boolean', 'box' => \true], 'CancelTaskInput' => ['type' => 'structure', 'required' => ['taskId'], 'members' => ['taskId' => ['shape' => 'TaskId', 'location' => 'uri', 'locationName' => 'taskId']]], 'CancelTaskOutput' => ['type' => 'structure', 'members' => ['taskId' => ['shape' => 'String']]], 'Capacity' => ['type' => 'structure', 'members' => ['available' => ['shape' => 'Long'], 'name' => ['shape' => 'CapacityNameString'], 'total' => ['shape' => 'Long'], 'unit' => ['shape' => 'CapacityUnitString'], 'used' => ['shape' => 'Long']]], 'CapacityList' => ['type' => 'list', 'member' => ['shape' => 'Capacity'], 'max' => 100, 'min' => 0], 'CapacityNameString' => ['type' => 'string', 'max' => 100, 'min' => 0], 'CapacityUnitString' => ['type' => 'string', 'max' => 20, 'min' => 0], 'Command' => ['type' => 'structure', 'members' => ['reboot' => ['shape' => 'Reboot'], 'unlock' => ['shape' => 'Unlock']], 'union' => \true], 'CpuOptions' => ['type' => 'structure', 'members' => ['coreCount' => ['shape' => 'Integer'], 'threadsPerCore' => ['shape' => 'Integer']]], 'CreateTaskInput' => ['type' => 'structure', 'required' => ['command', 'targets'], 'members' => ['clientToken' => ['shape' => 'IdempotencyToken', 'idempotencyToken' => \true], 'command' => ['shape' => 'Command'], 'description' => ['shape' => 'TaskDescriptionString'], 'tags' => ['shape' => 'TagMap'], 'targets' => ['shape' => 'TargetList']]], 'CreateTaskOutput' => ['type' => 'structure', 'members' => ['taskArn' => ['shape' => 'String'], 'taskId' => ['shape' => 'String']]], 'DescribeDeviceEc2Input' => ['type' => 'structure', 'required' => ['instanceIds', 'managedDeviceId'], 'members' => ['instanceIds' => ['shape' => 'InstanceIdsList'], 'managedDeviceId' => ['shape' => 'ManagedDeviceId', 'location' => 'uri', 'locationName' => 'managedDeviceId']]], 'DescribeDeviceEc2Output' => ['type' => 'structure', 'members' => ['instances' => ['shape' => 'InstanceSummaryList']]], 'DescribeDeviceInput' => ['type' => 'structure', 'required' => ['managedDeviceId'], 'members' => ['managedDeviceId' => ['shape' => 'ManagedDeviceId', 'location' => 'uri', 'locationName' => 'managedDeviceId']]], 'DescribeDeviceOutput' => ['type' => 'structure', 'members' => ['associatedWithJob' => ['shape' => 'String'], 'deviceCapacities' => ['shape' => 'CapacityList'], 'deviceState' => ['shape' => 'UnlockState'], 'deviceType' => ['shape' => 'String'], 'lastReachedOutAt' => ['shape' => 'Timestamp'], 'lastUpdatedAt' => ['shape' => 'Timestamp'], 'managedDeviceArn' => ['shape' => 'String'], 'managedDeviceId' => ['shape' => 'ManagedDeviceId'], 'physicalNetworkInterfaces' => ['shape' => 'PhysicalNetworkInterfaceList'], 'software' => ['shape' => 'SoftwareInformation'], 'tags' => ['shape' => 'TagMap']]], 'DescribeExecutionInput' => ['type' => 'structure', 'required' => ['managedDeviceId', 'taskId'], 'members' => ['managedDeviceId' => ['shape' => 'ManagedDeviceId', 'location' => 'uri', 'locationName' => 'managedDeviceId'], 'taskId' => ['shape' => 'TaskId', 'location' => 'uri', 'locationName' => 'taskId']]], 'DescribeExecutionOutput' => ['type' => 'structure', 'members' => ['executionId' => ['shape' => 'ExecutionId'], 'lastUpdatedAt' => ['shape' => 'Timestamp'], 'managedDeviceId' => ['shape' => 'ManagedDeviceId'], 'startedAt' => ['shape' => 'Timestamp'], 'state' => ['shape' => 'ExecutionState'], 'taskId' => ['shape' => 'TaskId']]], 'DescribeTaskInput' => ['type' => 'structure', 'required' => ['taskId'], 'members' => ['taskId' => ['shape' => 'TaskId', 'location' => 'uri', 'locationName' => 'taskId']]], 'DescribeTaskOutput' => ['type' => 'structure', 'members' => ['completedAt' => ['shape' => 'Timestamp'], 'createdAt' => ['shape' => 'Timestamp'], 'description' => ['shape' => 'TaskDescriptionString'], 'lastUpdatedAt' => ['shape' => 'Timestamp'], 'state' => ['shape' => 'TaskState'], 'tags' => ['shape' => 'TagMap'], 'targets' => ['shape' => 'TargetList'], 'taskArn' => ['shape' => 'String'], 'taskId' => ['shape' => 'String']]], 'DeviceSummary' => ['type' => 'structure', 'members' => ['associatedWithJob' => ['shape' => 'String'], 'managedDeviceArn' => ['shape' => 'String'], 'managedDeviceId' => ['shape' => 'ManagedDeviceId'], 'tags' => ['shape' => 'TagMap']]], 'DeviceSummaryList' => ['type' => 'list', 'member' => ['shape' => 'DeviceSummary']], 'EbsInstanceBlockDevice' => ['type' => 'structure', 'members' => ['attachTime' => ['shape' => 'Timestamp'], 'deleteOnTermination' => ['shape' => 'Boolean'], 'status' => ['shape' => 'AttachmentStatus'], 'volumeId' => ['shape' => 'String']]], 'ExecutionId' => ['type' => 'string', 'max' => 64, 'min' => 1], 'ExecutionState' => ['type' => 'string', 'enum' => ['QUEUED', 'IN_PROGRESS', 'CANCELED', 'FAILED', 'SUCCEEDED', 'REJECTED', 'TIMED_OUT']], 'ExecutionSummary' => ['type' => 'structure', 'members' => ['executionId' => ['shape' => 'ExecutionId'], 'managedDeviceId' => ['shape' => 'ManagedDeviceId'], 'state' => ['shape' => 'ExecutionState'], 'taskId' => ['shape' => 'TaskId']]], 'ExecutionSummaryList' => ['type' => 'list', 'member' => ['shape' => 'ExecutionSummary']], 'IdempotencyToken' => ['type' => 'string', 'max' => 64, 'min' => 1, 'pattern' => '[!-~]+'], 'Instance' => ['type' => 'structure', 'members' => ['amiLaunchIndex' => ['shape' => 'Integer'], 'blockDeviceMappings' => ['shape' => 'InstanceBlockDeviceMappingList'], 'cpuOptions' => ['shape' => 'CpuOptions'], 'createdAt' => ['shape' => 'Timestamp'], 'imageId' => ['shape' => 'String'], 'instanceId' => ['shape' => 'String'], 'instanceType' => ['shape' => 'String'], 'privateIpAddress' => ['shape' => 'String'], 'publicIpAddress' => ['shape' => 'String'], 'rootDeviceName' => ['shape' => 'String'], 'securityGroups' => ['shape' => 'SecurityGroupIdentifierList'], 'state' => ['shape' => 'InstanceState'], 'updatedAt' => ['shape' => 'Timestamp']]], 'InstanceBlockDeviceMapping' => ['type' => 'structure', 'members' => ['deviceName' => ['shape' => 'String'], 'ebs' => ['shape' => 'EbsInstanceBlockDevice']]], 'InstanceBlockDeviceMappingList' => ['type' => 'list', 'member' => ['shape' => 'InstanceBlockDeviceMapping']], 'InstanceIdsList' => ['type' => 'list', 'member' => ['shape' => 'String']], 'InstanceState' => ['type' => 'structure', 'members' => ['code' => ['shape' => 'Integer'], 'name' => ['shape' => 'InstanceStateName']]], 'InstanceStateName' => ['type' => 'string', 'enum' => ['PENDING', 'RUNNING', 'SHUTTING_DOWN', 'TERMINATED', 'STOPPING', 'STOPPED']], 'InstanceSummary' => ['type' => 'structure', 'members' => ['instance' => ['shape' => 'Instance'], 'lastUpdatedAt' => ['shape' => 'Timestamp']]], 'InstanceSummaryList' => ['type' => 'list', 'member' => ['shape' => 'InstanceSummary']], 'Integer' => ['type' => 'integer', 'box' => \true], 'InternalServerException' => ['type' => 'structure', 'required' => ['message'], 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 500], 'exception' => \true, 'fault' => \true, 'retryable' => ['throttling' => \false]], 'IpAddressAssignment' => ['type' => 'string', 'enum' => ['DHCP', 'STATIC']], 'JobId' => ['type' => 'string', 'max' => 64, 'min' => 1], 'ListDeviceResourcesInput' => ['type' => 'structure', 'required' => ['managedDeviceId'], 'members' => ['managedDeviceId' => ['shape' => 'ManagedDeviceId', 'location' => 'uri', 'locationName' => 'managedDeviceId'], 'maxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => 'NextToken', 'location' => 'querystring', 'locationName' => 'nextToken'], 'type' => ['shape' => 'ListDeviceResourcesInputTypeString', 'location' => 'querystring', 'locationName' => 'type']]], 'ListDeviceResourcesInputTypeString' => ['type' => 'string', 'max' => 50, 'min' => 1], 'ListDeviceResourcesOutput' => ['type' => 'structure', 'members' => ['nextToken' => ['shape' => 'NextToken'], 'resources' => ['shape' => 'ResourceSummaryList']]], 'ListDevicesInput' => ['type' => 'structure', 'members' => ['jobId' => ['shape' => 'JobId', 'location' => 'querystring', 'locationName' => 'jobId'], 'maxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => 'NextToken', 'location' => 'querystring', 'locationName' => 'nextToken']]], 'ListDevicesOutput' => ['type' => 'structure', 'members' => ['devices' => ['shape' => 'DeviceSummaryList'], 'nextToken' => ['shape' => 'NextToken']]], 'ListExecutionsInput' => ['type' => 'structure', 'required' => ['taskId'], 'members' => ['maxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => 'NextToken', 'location' => 'querystring', 'locationName' => 'nextToken'], 'state' => ['shape' => 'ExecutionState', 'location' => 'querystring', 'locationName' => 'state'], 'taskId' => ['shape' => 'TaskId', 'location' => 'querystring', 'locationName' => 'taskId']]], 'ListExecutionsOutput' => ['type' => 'structure', 'members' => ['executions' => ['shape' => 'ExecutionSummaryList'], 'nextToken' => ['shape' => 'NextToken']]], 'ListTagsForResourceInput' => ['type' => 'structure', 'required' => ['resourceArn'], 'members' => ['resourceArn' => ['shape' => 'String', 'location' => 'uri', 'locationName' => 'resourceArn']]], 'ListTagsForResourceOutput' => ['type' => 'structure', 'members' => ['tags' => ['shape' => 'TagMap']]], 'ListTasksInput' => ['type' => 'structure', 'members' => ['maxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => 'NextToken', 'location' => 'querystring', 'locationName' => 'nextToken'], 'state' => ['shape' => 'TaskState', 'location' => 'querystring', 'locationName' => 'state']]], 'ListTasksOutput' => ['type' => 'structure', 'members' => ['nextToken' => ['shape' => 'NextToken'], 'tasks' => ['shape' => 'TaskSummaryList']]], 'Long' => ['type' => 'long', 'box' => \true], 'ManagedDeviceId' => ['type' => 'string', 'max' => 64, 'min' => 1], 'MaxResults' => ['type' => 'integer', 'box' => \true, 'max' => 100, 'min' => 1], 'NextToken' => ['type' => 'string', 'max' => 1024, 'min' => 1, 'pattern' => '[a-zA-Z0-9+/=]*'], 'PhysicalConnectorType' => ['type' => 'string', 'enum' => ['RJ45', 'SFP_PLUS', 'QSFP', 'RJ45_2', 'WIFI']], 'PhysicalNetworkInterface' => ['type' => 'structure', 'members' => ['defaultGateway' => ['shape' => 'String'], 'ipAddress' => ['shape' => 'String'], 'ipAddressAssignment' => ['shape' => 'IpAddressAssignment'], 'macAddress' => ['shape' => 'String'], 'netmask' => ['shape' => 'String'], 'physicalConnectorType' => ['shape' => 'PhysicalConnectorType'], 'physicalNetworkInterfaceId' => ['shape' => 'String']]], 'PhysicalNetworkInterfaceList' => ['type' => 'list', 'member' => ['shape' => 'PhysicalNetworkInterface']], 'Reboot' => ['type' => 'structure', 'members' => []], 'ResourceNotFoundException' => ['type' => 'structure', 'required' => ['message'], 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 404, 'senderFault' => \true], 'exception' => \true], 'ResourceSummary' => ['type' => 'structure', 'required' => ['resourceType'], 'members' => ['arn' => ['shape' => 'String'], 'id' => ['shape' => 'String'], 'resourceType' => ['shape' => 'String']]], 'ResourceSummaryList' => ['type' => 'list', 'member' => ['shape' => 'ResourceSummary']], 'SecurityGroupIdentifier' => ['type' => 'structure', 'members' => ['groupId' => ['shape' => 'String'], 'groupName' => ['shape' => 'String']]], 'SecurityGroupIdentifierList' => ['type' => 'list', 'member' => ['shape' => 'SecurityGroupIdentifier']], 'ServiceQuotaExceededException' => ['type' => 'structure', 'required' => ['message'], 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 402, 'senderFault' => \true], 'exception' => \true], 'SoftwareInformation' => ['type' => 'structure', 'members' => ['installState' => ['shape' => 'String'], 'installedVersion' => ['shape' => 'String'], 'installingVersion' => ['shape' => 'String']]], 'String' => ['type' => 'string'], 'TagKeys' => ['type' => 'list', 'member' => ['shape' => 'String']], 'TagMap' => ['type' => 'map', 'key' => ['shape' => 'String'], 'value' => ['shape' => 'String']], 'TagResourceInput' => ['type' => 'structure', 'required' => ['resourceArn', 'tags'], 'members' => ['resourceArn' => ['shape' => 'String', 'location' => 'uri', 'locationName' => 'resourceArn'], 'tags' => ['shape' => 'TagMap']]], 'TargetList' => ['type' => 'list', 'member' => ['shape' => 'String'], 'max' => 10, 'min' => 1], 'TaskDescriptionString' => ['type' => 'string', 'max' => 128, 'min' => 1, 'pattern' => '[A-Za-z0-9 _.,!#]*'], 'TaskId' => ['type' => 'string', 'max' => 64, 'min' => 1], 'TaskState' => ['type' => 'string', 'enum' => ['IN_PROGRESS', 'CANCELED', 'COMPLETED']], 'TaskSummary' => ['type' => 'structure', 'required' => ['taskId'], 'members' => ['state' => ['shape' => 'TaskState'], 'tags' => ['shape' => 'TagMap'], 'taskArn' => ['shape' => 'String'], 'taskId' => ['shape' => 'TaskId']]], 'TaskSummaryList' => ['type' => 'list', 'member' => ['shape' => 'TaskSummary']], 'ThrottlingException' => ['type' => 'structure', 'required' => ['message'], 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 429, 'senderFault' => \true], 'exception' => \true, 'retryable' => ['throttling' => \true]], 'Timestamp' => ['type' => 'timestamp'], 'Unlock' => ['type' => 'structure', 'members' => []], 'UnlockState' => ['type' => 'string', 'enum' => ['UNLOCKED', 'LOCKED', 'UNLOCKING']], 'UntagResourceInput' => ['type' => 'structure', 'required' => ['resourceArn', 'tagKeys'], 'members' => ['resourceArn' => ['shape' => 'String', 'location' => 'uri', 'locationName' => 'resourceArn'], 'tagKeys' => ['shape' => 'TagKeys', 'location' => 'querystring', 'locationName' => 'tagKeys']]], 'ValidationException' => ['type' => 'structure', 'required' => ['message'], 'members' => ['message' => ['shape' => 'String']], 'error' => ['httpStatusCode' => 400, 'senderFault' => \true], 'exception' => \true]]];

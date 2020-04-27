<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="format-detection" content="telephone=no" /> 
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;" />
		<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />

		<title><?php echo $this->get_health_report()->get_report_subject(); ?></title>

		<style type="text/css">
			body {
				font-family: 'Helvetica', 'Arial', sans-serif;
				font-size: 14px;
				background: #eeeeee;
			}

			/* GENERAL STYLE RESETS */
			body, #bodyTable { height:100% !important; width:100% !important; margin:0; padding:0; }
			img, a img { border:0; outline:none; text-decoration:none; }
			.imageFix { display:block; }
			table, td { border-collapse:collapse; }

			/* CLIENT-SPECIFIC RESETS */
			.ReadMsgBody{width:100%;} .ExternalClass{width:100%;} 
			.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%;} 
			table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} 
			img{-ms-interpolation-mode: bicubic;} 
			body, table, td, p, a, li, blockquote{-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%;}
		</style>
	</head>

	<body style="padding:0; margin:0; background-color: #eeeeee;" bgcolor="#eeeeee">

		<table border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0" width="100%">
			<tr>
				<!-- wrap the main contents of the email -->
				<td align="left" style="width: 100% !important; height: 100px !important; padding: 20px;" valign="top">
					<table width="640" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="background-color: #ffffff;">
						<tr>
							<td>
								<?php $this->render_view( 'health-report/header' ); ?>
							</td>
						</tr>
						<tr>
							<td style="padding: 20px;">
							<?php
							$this->render_view( 'health-report/emails-sent' );
							$this->render_view( 'health-report/email-failures' );

							if ( ! $this->is_pro() ) {
								$this->render_view( 'health-report/upgrade' );;
							}
							?>
							</td>
						</tr>
						<tr>
							<td>
								<?php $this->render_view( 'health-report/footer' ); ?>
							</td>
						</tr>
					</table><!-- 640px table -->
				</td><!-- end main contents wrap -->
			</tr>
		</table><!-- 100% width table -->

	</body>

</html>

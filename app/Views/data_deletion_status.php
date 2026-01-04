<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 40px; 
            background: #f5f5f5; 
            line-height: 1.6;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .success { 
            color: #28a745; 
            font-size: 18px; 
            margin-bottom: 20px; 
            font-weight: bold;
        }
        .processing { 
            color: #ffc107; 
            font-size: 18px; 
            margin-bottom: 20px; 
            font-weight: bold;
        }
        .failed { 
            color: #dc3545; 
            font-size: 18px; 
            margin-bottom: 20px; 
            font-weight: bold;
        }
        .hint {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-style: italic;
        }
        .code { 
            background: #f8f9fa; 
            padding: 10px; 
            border-radius: 4px; 
            font-family: monospace; 
            border: 1px solid #dee2e6;
            word-break: break-all;
        }
        .info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo lang('Data Deletion Request'); ?></h1>
        
        <?php if ($status == 'success'): ?>
            <div class="success">✓ <?php echo lang('Your data has been successfully deleted from our system.'); ?></div>
        <?php elseif ($status == 'processing'): ?>
            <div class="processing">⏳ <?php echo lang('Your data deletion request is currently being processed...'); ?></div>
        <?php elseif ($status == 'failed'): ?>
            <div class="failed">✗ <?php echo lang('There was an error processing your data deletion request.'); ?></div>
        <?php else: ?>
            <div class="success">✓ <?php echo lang('Your data deletion request has been received.'); ?></div>
        <?php endif; ?>
        
        <div class="info">
            <strong><?php echo lang('Confirmation Details'); ?>:</strong><br>
            <?php echo lang('Confirmation Code'); ?>: <span class="code"><?php echo htmlspecialchars($confirmation_code); ?></span><br>
            <?php echo lang('Status'); ?>: <strong><?php echo ucfirst($status); ?></strong><br>
            <?php echo lang('Date'); ?>: <?php echo date('Y-m-d H:i:s'); ?>
        </div>
        
        <?php if ($show_processing_hint): ?>
            <div class="hint">
                <strong>⏰ <?php echo lang('Processing Notice'); ?>:</strong><br>
                <?php echo lang('Your data deletion is currently in progress. This process may take a few minutes to complete as we securely remove all your personal data from our system. Once the deletion is finished, this page will automatically update to show a success message. Please keep this page open or check back in a few minutes.'); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($status == 'success'): ?>
            <p><?php echo $this->lang->line("We have successfully processed your data deletion request and removed all your personal data from our system in accordance with Facebook's data deletion requirements."); ?></p>
            
            <p><strong><?php echo lang('What was deleted'); ?>:</strong></p>
            <ul>
                <li><?php echo lang('Your Facebook account information'); ?></li>
                <li><?php echo lang('All associated page and group data'); ?></li>
                <li><?php echo lang('Auto-reply campaigns and settings'); ?></li>
                <li><?php echo lang('Posting campaigns and schedules'); ?></li>
                <li><?php echo lang('Messenger bot interactions'); ?></li>
                <li><?php echo lang('All other personal data stored in our system'); ?></li>
            </ul>
        <?php elseif ($status == 'processing'): ?>
            <p><?php echo $this->lang->line("Your data deletion request has been received and is currently being processed. We are working to remove all your personal data from our system in accordance with Facebook's data deletion requirements."); ?></p>
            
            <p><strong><?php echo lang('What will be deleted'); ?>:</strong></p>
            <ul>
                <li><?php echo lang('Your Facebook account information'); ?></li>
                <li><?php echo lang('All associated page and group data'); ?></li>
                <li><?php echo lang('Auto-reply campaigns and settings'); ?></li>
                <li><?php echo lang('Posting campaigns and schedules'); ?></li>
                <li><?php echo lang('Messenger bot interactions'); ?></li>
                <li><?php echo lang('All other personal data stored in our system'); ?></li>
            </ul>
        <?php elseif ($status == 'failed'): ?>
            <p><?php echo lang('We encountered an error while processing your data deletion request. Please try again or contact our support team for assistance.'); ?></p>
        <?php else: ?>
            <p><?php echo $this->lang->line("Your data deletion request has been received and will be processed shortly. We will remove all your personal data from our system in accordance with Facebook's data deletion requirements."); ?></p>
        <?php endif; ?>
        
        <div class="footer">
            <p><?php echo lang('If you have any questions about this deletion or need further assistance, please contact our support team.'); ?></p>
            <p><strong><?php echo lang('Note'); ?>:</strong> <?php echo lang('This confirmation code serves as proof that your data deletion request has been'); ?> <?php echo $status == 'success' ? lang('completed') : lang('received'); ?>.</p>
        </div>
    </div>
</body>
</html>
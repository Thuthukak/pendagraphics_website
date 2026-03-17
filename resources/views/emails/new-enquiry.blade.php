<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #1f1f1f; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4682b4; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9fafb; padding: 20px; margin-top: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #262626ff; }
        .value { margin-top: 5px; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #4682b4; }
        .logo { max-width: 150px; margin-bottom: 10px;}
    </style>
</head>
<body>
    <div class="container">

        <div class="header">
            <img src="{{ asset('assets/images/penda_logo.png') }}" 
                alt="Logo"
                class="logo">
            <h2>New Enquiry Received</h2>
        </div>
        
        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">{{ $contact->name }}</div>
            </div>
            
            <div class="field">
                <div class="label">Company:</div>
                <div class="value">{{ $contact->company }}</div>
            </div>
            
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">{{ $contact->phone }}</div>
            </div>
            
            @if($contact->email)
            <div class="field">
                <div class="label">Email:</div>
                <div class="value">{{ $contact->email }}</div>
            </div>
            @endif
            
            <div class="field">
                <div class="label">Service:</div>
                <div class="value">{{ $contact->service }}</div>
            </div>

            <div class="field">
                <div class="label">Message:</div>
                <div class="value">{{ $contact->message }}</div>
            </div>
            
            <div class="field">
                <div class="label">Status:</div>
                <div class="value">{{ ucfirst($contact->status) }}</div>
            </div>
            
            <div class="field">
                <div class="label">Submitted at:</div>
                <div class="value">{{ $contact->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
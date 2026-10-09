
<html>
<head>
    <title></title>
</head>
<body>
<h2>Hi, </h2>
<p>
    The Guide has been emailed to {{$app->full_name }} of {{ $app->dyeo ? $app->dyeo->district_code: '' }}
</p>

<p>Thanks</p>
<p>Rotary Youth Exchange in Britain & Ireland</p>
</body>
</html>

{{--<h3>Email History:</h3>--}}
{{--<table style="border-collapse: collapse; border: 1px solid dimgrey;">--}}
{{--    <thead>--}}
{{--    <tr style="padding: 5px; border: 1px solid dimgrey;">--}}
{{--        <th style="padding: 5px; border: 1px solid dimgrey;">S.No</th>--}}
{{--        <th style="padding: 5px; border: 1px solid dimgrey;">Email Address</th>--}}
{{--        <th style="padding: 5px; border: 1px solid dimgrey;">Title</th>--}}
{{--        <th style="padding: 5px; border: 1px solid dimgrey;">Email Type</th>--}}
{{--        <th style="padding: 5px; border: 1px solid dimgrey;">Sent On</th>--}}
{{--    </tr>--}}
{{--    </thead>--}}
{{--    @foreach($emailSent as $row)--}}
{{--        <tr style="padding: 5px;border: 1px solid dimgrey;">--}}
{{--            <td style="padding: 5px; border: 1px solid dimgrey;">{{ $loop->index  + 1 }}</td>--}}
{{--            <td style="padding: 5px; border: 1px solid dimgrey;">{{ $row->email_address }}</td>--}}
{{--            <td style="padding: 5px; border: 1px solid dimgrey;">{{ $row->message_title }}</td>--}}
{{--            <td style="padding: 5px;border: 1px solid dimgrey;">{{ $row->email_type->email_type_label }}</td>--}}
{{--            <td style="padding: 5px; border: 1px solid dimgrey;">{{ $row->created_at }}</td>--}}
{{--        </tr>--}}
{{--    @endforeach--}}
{{--</table>--}}

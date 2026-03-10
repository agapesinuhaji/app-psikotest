<h2>Jawaban Peserta</h2>

<p>
Nama: {{ $user->name }} <br>
NIK: {{ $user->nik }} <br>
Batch: {{ $user->batch->name }}
</p>

<table border="1" width="100%" cellpadding="5">
<tr>
<th>No</th>
<th>Pertanyaan</th>
<th>Jawaban</th>
</tr>

@foreach($answers as $i => $answer)
<tr>
<td>{{ $i+1 }}</td>
<td>{{ $answer->question->question }}</td>
<td>{{ $answer->answer }}</td>
</tr>
@endforeach

</table>
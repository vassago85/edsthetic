@props(['content', 'number' => 4])
<section id="process" aria-labelledby="process-heading">
  <div class="wrap">
    <x-site.section-head
      :number="$number"
      title="How a job runs"
      heading-id="process-heading"
      lede="Drawings, materials and price are agreed in writing before anything is cut."
    />
    <div class="process">
      <ol class="steps">
        @foreach (\App\Support\SiteProfile::steps() as $index => $step)
        <li>
          <span class="num" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
          <h3>{{ $step['name'] }}</h3>
          <p>{{ $step['text'] }}</p>
        </li>
        @endforeach
      </ol>
      <aside class="jobsheet" aria-label="Typical kitchen timeline">
        <div class="jobsheet-head">Typical kitchen timeline</div>
        <table>
          @foreach ($content->lead_times as $row)
          <tr>
            <td class="stage">{{ $row['label'] }}</td>
            <td class="dots" aria-hidden="true"></td>
            <td class="dur">{{ $row['duration'] }}</td>
          </tr>
          @endforeach
        </table>
        <p class="note-soft">Times depend on the size of the job and board availability. Your quote gives the dates for your job.</p>
      </aside>
    </div>
  </div>
</section>

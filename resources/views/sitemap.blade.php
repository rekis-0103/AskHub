{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('home') }}</loc></url>
    @foreach($questions as $question)
        <url><loc>{{ $question->public_url }}</loc><lastmod>{{ $question->updated_at->toAtomString() }}</lastmod></url>
    @endforeach
</urlset>

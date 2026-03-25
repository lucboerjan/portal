@component('mail::message')
# ✅ Backup succesvol uitgevoerd

De backup is met succes aangemaakt en opgeslagen op de server.

@component('mail::panel')
📄 **Bestand:** {{ $filename }}
💾 **Grootte:** {{ $size }}
@endcomponent

De backup is beschikbaar op de server en kan daar worden geraadpleegd of gedownload.

@endcomponent
# specify the VCL syntax version to use
vcl 4.1;

# import vmod_dynamic for better backend name resolution
import dynamic;

# we won't use any static backend, but Varnish still need a default one
backend default none;

# set up a dynamic director for more info, see https://github.com/nigoroll/libvmod-dynamic/blob/master/src/vmod_dynamic.vcc
sub vcl_init {
        new d = dynamic.director(port = "80");
}

sub vcl_recv {
    # force the host header to match the backend (not all backends need it, but example.com does)
    set req.http.host = "127.0.0.1";
    # set the backend
    set req.backend_hint = d.backend("127.0.0.1");
}
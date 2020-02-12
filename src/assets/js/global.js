$(document).ready(()=>
{
    // Internet status
    window.connected = function() {
        $.post({
            url: document.location.href,
            cache: false,
            data: {
                do: "connection",
            },
            success: (a)=>
            {
                $("body>header>span.internet-status>span.connection").removeClass("connected disconnected unknown").addClass(a ? 'connected' : 'disconnected').prop("title", a ? "You are connected to the internet." : "You are not connected to the internet.");

            }
        });
    };

    // Internet status loop, check every minute
    window.connected();
    setTimeout(()=>
    {
        window.connected();
    }, 60000);



    // Navigation Bar Toggle
    $("header>i").click(()=>
    {
        if ( $("nav").hasClass("active") )
        {
            $("nav").removeClass("active");
        }
        else
        {
            $("nav").addClass("active");
        }
    });
    $("nav>.barrier").click(()=>
    {
        if ( $("nav").hasClass("active") )
        {
            $("nav").removeClass("active");
        }
    });


    // Navigation Bar Requests
    $("nav>ul>li").click((a)=>
    {
        console.log( window.appendToUrl( "view", a.currentTarget.id ));
        $.post({
            url: document.location.href,
            cache: false,
            data: {
                do: "template",
                callback: a.currentTarget.id,
            },
            success: (a)=>
            {

                $("body>div.viewport").html(a);
                if ( $("nav").hasClass("active") )
                {
                    $("nav").removeClass("active");
                }
            }
        });
    });

    // Appends a (key => value) to the url without reloading. Used for tracking subpages
    window.appendToUrl =  function(k, v)
    {
        let u = window.location.href.split('?');
        typeof u[1] === "undefined" ? u[1] = "" : null;
        if (u.length > 0)
        {
            let q = u[1]
                .split("&")
                .map(n => n
                    .split("=")
                );
            let t = q.length;
            for (let i=0;i<q.length;i++)
            {
                if ( q[i][0] === k)
                {
                    t = i;
                }
            }
            q[t] = [k, v];
            q = q.map(n => n.join("=")).join("&");
            if ( q.substring(0, 1) === "&" ) { q = q.substring(1); }
            window.history.replaceState({}, document.title, u[0] + '?' + q);
        }
    }
});
$(document).ready(()=>
{
    // Internet status
    window.connected = function() {
        // Initiate POST request
        $.post({
            url: document.location.href,
            cache: false,
            data: {
                do: "connection",
            },
            success: (a)=>
            {
                // Remove preexisting classes and bind corresponding one to connection element
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
        // Flip navigation class based off preexisting class
        if ( $("nav").hasClass("active") )
        {
            $("nav").removeClass("active");
        }
        else
        {
            $("nav").addClass("active");
        }
    });

    // Bind to barrier aswell
    $("nav>.barrier").click(()=>
    {
        if ( $("nav").hasClass("active") )
        {
            $("nav").removeClass("active");
        }
    });

    // Dashboard card redirects
    $("body>div >div>div>div>div.head>i").click((a) => {
        window.redirect(a.currentTarget.id)
    });

    // Navigation Bar Requests
    $("nav>ul>li").click((a)=>
    {
        window.redirect(a.currentTarget.id);
    });

    window.redirect = function(a)
    {
        // Initiate POST request
        $.post({
            url: document.location.href,
            cache: false,
            data: {
                do: "template",
                callback: a,
            },
            success: (b)=>
            {
                // Set viewport to response and bind view to url
                $("body>div.viewport").fadeTo(300, 0, ()=>
                {
                    setTimeout(()=>
                    {
                        $("body>div.viewport").html(b).fadeTo(300, 1);
                        $("body>div >div>div>div>div.head>i").click((c) => {
                            window.redirect(c.currentTarget.id);
                        });
                    }, 400);
                });
                window.appendToUrl('view', a);

                // Close navigation
                if ( $("nav").hasClass("active") )
                {
                    $("nav").removeClass("active");
                }
            }
        });
    }

    // Appends a (key => value) to the url without reloading. Used for tracking subpages
    window.appendToUrl =  function(k, v)
    {
        // u = [baseurl, params]
        let u = window.location.href.split('?');

        // set u[1] to "" if undefined
        typeof u[1] === "undefined" ? u[1] = "" : null;

        // Check that u isn't just ""
        if (u.length > 0)
        {
            // Explode u[1] into a 2D array [ [param1, value1], [param2, value2] ]
            let q = u[1]
                .split("&")
                .map(n => n
                    .split("=")
                );

            // Bind t to length of q
            let t = q.length;

            // Search for the requested value
            for (let i=0;i<q.length;i++)
            {
                if ( q[i][0] === k)
                {
                    t = i;
                }
            }

            // Set the new key
            q[t] = [k, v];

            // Reconstruct the url section
            q = q.map(n => n
                .join("=")
            ).join("&");

            // Remove extra & on beginning if it exists
            if ( q.substring(0, 1) === "&" ) { q = q.substring(1); }

            // Replace old URL with new URL in history object
            window.history.replaceState({}, document.title, u[0] + '?' + q);
        }
    }
});
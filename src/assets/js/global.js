$(document).ready( () =>
{
    /**
     * Check internet connection
     */
    window.connected = () =>
    {
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
                $("body>header>span.internet-status>span.connection")
                    .removeClass("connected disconnected unknown")
                    .addClass(a ? 'connected' : 'disconnected')
                    .prop("title", a ? "You are connected to the internet." : "You are not connected to the internet.");

            }
        });
    };

    /**
     * Reload this script
     */
    window.reload = () =>
    {
        $(" head > script.global ").remove();
        $(" head ").append("<script id=\"global\" src=\"assets/js/global.js\"></script>");
    };

    /**
     * Redirect through Ajax
     *
     * @param location
     * @param data
     */
    window.redirect = ( location, data = {} )=>
    {
        // Initiate POST request
        Object.assign(data, {
            do: "template",
            callback: location,
        });
        $.post({
            url: document.location.href,
            cache: false,
            data: data,
            success: (b)=>
            {
                // Set viewport to response and bind view to url
                $("body>div.viewport").fadeTo(300, 0, () =>
                {
                    setTimeout(()=>
                    {
                        // Fade in and reload
                        $("body>div.viewport").html(b).fadeTo(300, 1);
                        window.bind();
                    }, 400);
                });
                window.appendToUrl('view', location);

                // Close navigation
                if ( $("nav").hasClass("active") )
                {
                    $("nav").removeClass("active");
                }
            }
        });
    };

    /**
     * Appends a (key => value) to the url without reloading. Used for tracking subpages
     *
     * @param k Key
     * @param v Value
     */
    window.appendToUrl = ( k, v ) =>
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
    };

    /**
     * Table Pagination
     *
     * @param a ButtonElement
     */
    window.tablePagination = ( a ) =>
    {
        if (a.currentTarget.classList[1] !== "disabled")
        {
            console.log("Page: " + a.currentTarget.classList[1]);
            $.post({
                url: document.location.href,
                cache: false,
                data: {
                    do: "template",
                    callback: a.currentTarget.parentElement.parentElement.parentElement.classList[1],
                    pageAjax: true,
                    page: a.currentTarget.classList[1]
                },
                success: (b) =>
                {
                    $(a.currentTarget.parentElement.parentElement.parentElement.children[1].children[1]).fadeTo(200, 0, () =>
                    {
                        setTimeout(() => {
                            $(a.currentTarget.parentElement.parentElement.parentElement).html(b);
                            window.test = a.currentTarget.parentElement.parentElement.parentElement;
                            $("body > div > div > table > tbody").fadeTo(200, 1);
                            $("body>div >div>div>div>div.head>i").click( ( c ) =>
                            {
                                window.redirect(c.currentTarget.id);
                            });
                            $("body > div > div > div > div > div.button").click( ( d ) =>
                            {
                                window.tablePagination(d);
                            });
                        }, 200);
                    });
                }
            });
        }
    };

    /**
     * Emulate PHP's $_GET
     *
     * @param a URI
     * @returns {*}
     */
    window.get = ( a ) =>
    {
        let y = window.location.search.substr(1).split("&");
        let x = {};
        for ( let i = 0; i < y.length; i++ )
        {
            x[decodeURIComponent( y[ i ].split( "=" )[ 0 ] ) ] = decodeURIComponent( y[ i ].split( "=" )[ 1 ] );
        }
        return x[a];
    };

    /**
     * Viewport Attribute Binding
     */
    window.bind = () =>
    {
        // Dashboard card redirects
        $("body>div >div>div>div>div.head>i").click( ( a ) =>
        {
            window.redirect(a.currentTarget.id)
        });

        // Table buttons
        $("body > div > div > div > div > div.button").click( ( a ) =>
        {
            window.tablePagination(a);
        });

        // Table CTA Redirects
        $(" table > tbody > tr > td.call-to-action").click( ( a ) =>
        {
            let x;
            a.currentTarget.classList[1]
                .split("&")
                .map(n => n
                    .split("=")
                )
                .forEach((a)=>{
                    if ( a[0] == "view" ) x = a[1];
                    window.appendToUrl(a[0], a[1])
                });
            window.redirect( x );
        });

        // Display buttons
        $("body > div > div > div.button-container > div.button").click( ( a ) =>
        {
            switch ( a.currentTarget.classList[1] )
            {
                // Redirect to addAid, id is already present in the URI
                case 'add':
                    window.redirect( 'aidAdd');
                    break;

                case 'edit':
                    window.redirect( 'personEdit' );
                    break;

                case 'delete':
                    if ( confirm( "Are you sure you wish to delete Person #" + window.get( 'id' ) + "?\n\nWARNING: THIS CAN NOT BE UNDONE!" ) )
                    {
                        $.post({
                            url: document.location.href,
                            cache: false,
                            dataType: "json",
                            data: {
                                do: "push",
                                callback: "deletePerson",
                                id: window.get( 'id' ),
                            },
                            success: ( b ) =>
                            {
                                if ( b[0] )
                                {
                                    alert( "Person #" + window.get( 'id' ) + " has successfully been deleted!");
                                }
                                else
                                {
                                    alert( "Error encountered when deleting Person #" + window.get( 'id' ) + "!");
                                }
                                window.appendToUrl('id', "");
                                window.redirect("personList");
                            }
                        });
                    }
                    break;
            }
        });

    };

    // Run bind
    window.bind();

    // Internet status loop, check every minute
    window.connected();
    setTimeout( () =>
    {
        window.connected();
    }, 60000);

    // Navigation Bar Toggle
    $("header>i").click( () =>
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

    // Navigation Bar Requests
    $("nav>ul>li").click( ( a ) =>
    {
        window.redirect(a.currentTarget.id);
    });

    // Bind to barrier aswell
    $("nav>.barrier").click( () =>
    {
        if ( $("nav").hasClass("active") )
        {
            $("nav").removeClass("active");
        }
    });


});
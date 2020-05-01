$(document).ready( ()=>
{
    // Checkbox control manage
    $("div.viewport > form.form > div.elements > div.element").change( (a)=>
    {
        if ( $(a.currentTarget).children()[1].type === "checkbox" )
        {
            JSON.parse( $( a.currentTarget ).children()[ 1 ].getAttribute( "aria-controls" ) ).forEach( ( b ) =>
            {
                $($(a.currentTarget).children()[1]).is(':checked') ? $( "#" + b ).show() : $( "#" + b ).hide();
            });
        }
    });

    // Submit / Cancel processing
    $("form.form > div.button").click( ( a ) =>
    {
        switch ( a.currentTarget.classList[1] )
        {
            case "submit":
                let data =
                    {
                    do: "template",
                    callback: window.get("view"),
                    confirm: true,
                };

                data[ "form_" + $(a.currentTarget).parent()[0].id ] = {};

                $($(a.currentTarget).parent().children()[1]).children().toArray().forEach( ( b ) =>
                {
                    let id = $(b).attr("id"), type = $(b).attr("datatype"), res;
                    switch ( type )
                    {
                        case "number":
                        case "text":
                            data[ "form_" + $(a.currentTarget).parent()[0].id ][id] = $(b).children()[1].value;
                            break;

                        case "boolean":
                            data[ "form_" + $(a.currentTarget).parent()[0].id ][id] = $($(b).children()[1]).is(':checked');
                            break;

                        case "object":
                            let y = [];
                            $(b.children[1].children[2]).children().toArray().forEach((c, d) =>
                            {
                                if (d % 2 === 1)
                                {
                                    let e = {};
                                    for (let i=0;i<c.children.length - 1; i++)
                                    {
                                        e[c.children[i].children[0].innerHTML] = c.children[i].children[1].value;
                                    }
                                    y.push(e);
                                }
                            });
                            data[ "form_" + $(a.currentTarget).parent()[0].id ][id] = y;

                            break;

                        case "array":
                            let z = {};
                            $(b.children[1].children[1]).children().toArray().forEach((c, d) =>
                            {
                                if (d < b.children[1].children[1].children.length - 1)
                                {
                                    z[c.children[0].value] = c.children[2].value;
                                }
                            });
                            data[ "form_" + $(a.currentTarget).parent()[0].id ][id] = z;
                            break;

                    }
                });
                data[ "form_" + $(a.currentTarget).parent()[0].id ] = JSON.stringify( data[ "form_" + $(a.currentTarget).parent()[0].id ] );
                $.post({
                    url: document.location.href,
                    cache: false,
                    data: data,
                    success: (b)=>
                    {
                        try
                        {
                            b = JSON.parse(b);
                            $(a.currentTarget)
                                .parent()
                                .children()
                                .eq(1)
                                .children()
                                .each((c, d)=>
                                {
                                    if (b.includes(d.id))
                                    {
                                        $(d).addClass("required");
                                        $(d).append("<div class='required-alert'>* Required</div>");
                                    }
                                });

                        } catch(e)
                        {
                            window.redirect(b);
                        }
                    }
                });
                break;

            case "cancel":
                window.redirect( JSON.parse(
                    $(a.currentTarget)
                        .parent()
                        .children()
                        .eq(4)
                        .html()
                ).cancel);
                break;

        }

        });

    // Initial processing, cycle through each element
    $( $( "form.form div.elements" ).children().toArray().forEach( ( a ) =>
    {

        switch ($(a).attr("datatype"))
        {
            case "boolean":
                JSON.parse($(a).children()[1].getAttribute("aria-controls")).forEach( ( b ) =>
                {
                    $( $( a ).children()[1] ).is( ':checked' ) ? $( "#" + b ).show() : $( "#" + b ).hide();
                });
                break;

            case "array":
                let x = JSON.parse( a.children[1].children[0].getAttribute( "value" ) );
                Object
                    .keys( x )
                    .forEach( ( b ) =>
                    {
                        $( a.children[1].children[1] )
                            .append(
                                "<div class=\"array-element\">" +
                                    "<input class=\"array-element-name\" type=\"text\" value=\"" + b + "\" />"+
                                    "<div class=\"array-element-remove button\">Remove</div>" +
                                    "<textarea class=\"array-element-value\" cols=\"30\" type=\"text\">" + x[b] + "</textarea>" +
                                "</div>"
                            )
                            .children()
                            .last()
                            .children()
                            .eq(1)
                            .click( ( a ) =>
                            {
                                $(a.currentTarget).parent().remove();
                            });

                    });
                $( a.children[1].children[1] ).append("<div class=\"button\">Add</div>");

                $(a.children[1].children[1].children[a.children[1].children[1].children.length - 1]).click( (b) =>
                {
                     $( b.currentTarget ).before(
                         "<div class=\"array-element\">" +
                            "<input class=\"array-element-name\" type=\"text\" value=\"\" />"+
                            "<div class=\"array-element-remove button\">Remove</div>" +
                            "<textarea class=\"array-element-value\" cols=\"30\" type=\"text\"></textarea>" +
                         "</div>"
                     );
                     $($(b.currentTarget)
                         .parent()
                         .children()[$(b.currentTarget).parent().children().length - 2]
                         .children[1])
                         .click((a)=>
                         {
                             $(a.currentTarget).parent().remove()
                         });

                });
                break;

            case "object":
                let value = JSON.parse( a.children[1].children[1].getAttribute( "value" ) );

                // # Generate an anonymous function to manage the object template
                // ## Dynamic-dynamic-deceleration of an anonymous(struct) function. :barf:
                let obj = JSON.parse( a.children[1].children[0].getAttribute( "value" ) );
                let y = "return '<div class=\"object-name\"> \' + v[0] + \'</div><div class=\"object-element\">";
                for ( let i=0; i< Object.keys( obj ).length; i++ )
                {
                    let v = obj[ Object.keys( obj )[ i ] ];
                    y += '<div class="object-kv-container"><div class="object-name">' + Object.keys(obj)[i].substr(0, 1).toUpperCase() + Object.keys(obj)[i].substr(1) + '</div>';
                    switch (typeof v)
                    {
                        case "object":
                            y += '<select class="object object-select ' + Object.keys(obj)[i] + '" value="\' + v['+ (i+1) +'] + \'">';
                            v.forEach((f) =>
                            {
                                y += '<option \' + ( v['+ (i+1) +'] == \"' + f + '\" ? \"selected\" : null) + \' value="' + f + '">' + f + '</option>';
                            });
                            y += "</select>";
                            break;

                        case "number":
                            y += '<input class="object object-number ' + Object.keys(obj)[i] + '" value="\' + v['+ (i+1) +'] + \'" />';
                            break;

                        case "string":
                            y += '<input class="object object-string ' + Object.keys(obj)[i] + '"  value="\' + v['+ (i+1) +'] + \'" />';
                            break;
                    }
                    y += "</div>";
                }
                y += "<div class=\"object-element-remove button\">Remove</div>";
                let o =  new Function( "v", y + "</div>'" ), l;
                Object.values( value ).forEach( ( c, i ) =>
                {
                    // v0 => name, v>0... => vals
                    v = ["Entry #" + (i + 1) ];
                    Object.keys( obj ).forEach( ( a ) =>
                    {
                        v.push( c[ a ] );
                    });
                    l = c.length;
                    $( a.children[ 1 ].children[ 2 ] )
                        .append( o( v ) )
                        .children()
                        .last()
                        .children()
                        .last()
                        .click( ( b ) =>
                        {
                            $(b.currentTarget)
                                .parent()
                                .parent()
                                .children()
                                .eq( $(b.currentTarget)
                                    .parent()
                                    .index() - 1 )
                                .remove();
                            $(b.currentTarget)
                                .parent()
                                .remove();
                        });
                });

                $( a.children[1].children[2] ).append("<div class=\"button\">Add</div>");
                /**
                 * @TODO convert selector navigation to jQuery
                 */
                $( a.children[1].children[2].children[a.children[1].children[2].children.length - 1] ).click( ( b ) =>
                {
                   $( b.currentTarget )
                       .before(
                           o(
                               [
                                   ...[
                                   "Entry #" + Math.ceil(
                                       $( b.currentTarget )
                                           .parent()
                                           .children()
                                           .length / 2
                                   )
                                   ],
                                   ...[].fill("", 0, l)
                               ]
                           )
                       );

                   $(b.currentTarget)
                       .parent()
                       .children()
                       .eq( $(b.currentTarget)
                           .parent()
                           .children()
                           .length - 2)
                       .children()
                       .eq(3)
                       .click( ( a )=>
                       {
                            $(a.currentTarget)
                                .parent()
                                .parent()
                                .children()
                                .eq( $(a.currentTarget)
                                    .parent()
                                    .index() - 1)
                                .remove();

                            $(a.currentTarget)
                                .parent()
                                .remove();

                       });
                });
                break;
        }
    }));

});
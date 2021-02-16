# Moodle plugin local_quickregister

This plugin provides a quick registration link with prefilled signup form data.

It adds 2 admin pages :
1. A setting page to define or generate a random private key used to validate the data.
2. A link generator page to generate the registration URL and HTML link.

## Standalone link generator
The plugin adds a public standalone page at local/quickregister/link_generator.html

## Installation
Copy files in local/quickregister of your Moodle directory.

## Campaign plugin
This plugins also works with local_campaign plugin, if enabled you can add current campaign in registration data.

## HTML File

```html
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" rel="stylesheet">
    <title>Quickregister link generator</title>
    <style>
        body {
            margin: 15px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Quickregister link generator</h1>

    <form class="needs-validation" id="form" novalidate>
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Key</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="key">Key</label>
                    <input class="form-control" id="key" required type="text">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">User informations</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <input class="form-control" id="username" type="text">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" id="password" type="password">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" id="email" type="email">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="firstname">Firstname</label>
                    <input class="form-control" id="firstname" type="text">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="lastname">Lastname</label>
                    <input class="form-control" id="lastname" type="text">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="city">City/town</label>
                    <input class="form-control" id="city" type="text">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="country">Country</label>
                    <select class="form-select" id="country">
                        <option value="">Select a country</option>
                    </select>
                </div>
            </div>
        </div>

        <!--
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Campaign</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="campaign" class="form-label">Campaign</label>
                    <input type="text" class="form-control" id="campaign">
                </div>
            </div>
        </div>
        -->

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Link informations</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="link-url">Link URL</label>
                    <input class="form-control" id="link-url" required type="url" value="https://example.com/login/signup.php">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="link-anchor">Link anchor</label>
                    <input class="form-control" id="link-anchor" required type="text" value="Register">
                </div>
            </div>
        </div>

        <div class="d-grid mb-3">
            <button class="btn btn-primary" type="submit">Generate</button>
        </div>

        <div id="result" style="display: none;">
            <div class="mb-3">
                <label class="form-label" for="result-url">URL</label>
                <input class="form-control" id="result-url" type="url">
                <button class="btn btn-secondary mt-1" onclick="copyToClipboard('#result-url');" type="button">Copy to clipboard</button>
            </div>
            <div class="mb-3">
                <label class="form-label" for="result-link">HTML link</label>
                <textarea class="form-control" cols="30" id="result-link" rows="10"></textarea>
                <button class="btn btn-secondary mt-1" onclick="copyToClipboard('#result-link');" type="button">Copy to clipboard</button>
            </div>
        </div>
    </form>
</div>

<script crossorigin="anonymous" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // https://getbootstrap.com/docs/5.0/forms/validation/
        let forms = document.querySelectorAll('.needs-validation')

        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })

        let form = document.getElementById('form');
        let result = document.getElementById('result');
        // Countries from https://github.com/umpirsky/country-list
        let countries = JSON.parse('{"AF":"Afghanistan","AX":"\u00c5land Islands","AL":"Albania","DZ":"Algeria","AS":"American Samoa","AD":"Andorra","AO":"Angola","AI":"Anguilla","AQ":"Antarctica","AG":"Antigua & Barbuda","AR":"Argentina","AM":"Armenia","AW":"Aruba","AU":"Australia","AT":"Austria","AZ":"Azerbaijan","BS":"Bahamas","BH":"Bahrain","BD":"Bangladesh","BB":"Barbados","BY":"Belarus","BE":"Belgium","BZ":"Belize","BJ":"Benin","BM":"Bermuda","BT":"Bhutan","BO":"Bolivia","BA":"Bosnia & Herzegovina","BW":"Botswana","BV":"Bouvet Island","BR":"Brazil","IO":"British Indian Ocean Territory","VG":"British Virgin Islands","BN":"Brunei","BG":"Bulgaria","BF":"Burkina Faso","BI":"Burundi","KH":"Cambodia","CM":"Cameroon","CA":"Canada","CV":"Cape Verde","BQ":"Caribbean Netherlands","KY":"Cayman Islands","CF":"Central African Republic","TD":"Chad","CL":"Chile","CN":"China","CX":"Christmas Island","CC":"Cocos (Keeling) Islands","CO":"Colombia","KM":"Comoros","CG":"Congo - Brazzaville","CD":"Congo - Kinshasa","CK":"Cook Islands","CR":"Costa Rica","CI":"C\u00f4te d\u2019Ivoire","HR":"Croatia","CU":"Cuba","CW":"Cura\u00e7ao","CY":"Cyprus","CZ":"Czechia","DK":"Denmark","DJ":"Djibouti","DM":"Dominica","DO":"Dominican Republic","EC":"Ecuador","EG":"Egypt","SV":"El Salvador","GQ":"Equatorial Guinea","ER":"Eritrea","EE":"Estonia","SZ":"Eswatini","ET":"Ethiopia","FK":"Falkland Islands","FO":"Faroe Islands","FJ":"Fiji","FI":"Finland","FR":"France","GF":"French Guiana","PF":"French Polynesia","TF":"French Southern Territories","GA":"Gabon","GM":"Gambia","GE":"Georgia","DE":"Germany","GH":"Ghana","GI":"Gibraltar","GR":"Greece","GL":"Greenland","GD":"Grenada","GP":"Guadeloupe","GU":"Guam","GT":"Guatemala","GG":"Guernsey","GN":"Guinea","GW":"Guinea-Bissau","GY":"Guyana","HT":"Haiti","HM":"Heard & McDonald Islands","HN":"Honduras","HK":"Hong Kong SAR China","HU":"Hungary","IS":"Iceland","IN":"India","ID":"Indonesia","IR":"Iran","IQ":"Iraq","IE":"Ireland","IM":"Isle of Man","IL":"Israel","IT":"Italy","JM":"Jamaica","JP":"Japan","JE":"Jersey","JO":"Jordan","KZ":"Kazakhstan","KE":"Kenya","KI":"Kiribati","KW":"Kuwait","KG":"Kyrgyzstan","LA":"Laos","LV":"Latvia","LB":"Lebanon","LS":"Lesotho","LR":"Liberia","LY":"Libya","LI":"Liechtenstein","LT":"Lithuania","LU":"Luxembourg","MO":"Macao SAR China","MG":"Madagascar","MW":"Malawi","MY":"Malaysia","MV":"Maldives","ML":"Mali","MT":"Malta","MH":"Marshall Islands","MQ":"Martinique","MR":"Mauritania","MU":"Mauritius","YT":"Mayotte","MX":"Mexico","FM":"Micronesia","MD":"Moldova","MC":"Monaco","MN":"Mongolia","ME":"Montenegro","MS":"Montserrat","MA":"Morocco","MZ":"Mozambique","MM":"Myanmar (Burma)","NA":"Namibia","NR":"Nauru","NP":"Nepal","NL":"Netherlands","NC":"New Caledonia","NZ":"New Zealand","NI":"Nicaragua","NE":"Niger","NG":"Nigeria","NU":"Niue","NF":"Norfolk Island","KP":"North Korea","MK":"North Macedonia","MP":"Northern Mariana Islands","NO":"Norway","OM":"Oman","PK":"Pakistan","PW":"Palau","PS":"Palestinian Territories","PA":"Panama","PG":"Papua New Guinea","PY":"Paraguay","PE":"Peru","PH":"Philippines","PN":"Pitcairn Islands","PL":"Poland","PT":"Portugal","PR":"Puerto Rico","QA":"Qatar","RE":"R\u00e9union","RO":"Romania","RU":"Russia","RW":"Rwanda","WS":"Samoa","SM":"San Marino","ST":"S\u00e3o Tom\u00e9 & Pr\u00edncipe","SA":"Saudi Arabia","SN":"Senegal","RS":"Serbia","SC":"Seychelles","SL":"Sierra Leone","SG":"Singapore","SX":"Sint Maarten","SK":"Slovakia","SI":"Slovenia","SB":"Solomon Islands","SO":"Somalia","ZA":"South Africa","GS":"South Georgia & South Sandwich Islands","KR":"South Korea","SS":"South Sudan","ES":"Spain","LK":"Sri Lanka","BL":"St. Barth\u00e9lemy","SH":"St. Helena","KN":"St. Kitts & Nevis","LC":"St. Lucia","MF":"St. Martin","PM":"St. Pierre & Miquelon","VC":"St. Vincent & Grenadines","SD":"Sudan","SR":"Suriname","SJ":"Svalbard & Jan Mayen","SE":"Sweden","CH":"Switzerland","SY":"Syria","TW":"Taiwan","TJ":"Tajikistan","TZ":"Tanzania","TH":"Thailand","TL":"Timor-Leste","TG":"Togo","TK":"Tokelau","TO":"Tonga","TT":"Trinidad & Tobago","TN":"Tunisia","TR":"Turkey","TM":"Turkmenistan","TC":"Turks & Caicos Islands","TV":"Tuvalu","UM":"U.S. Outlying Islands","VI":"U.S. Virgin Islands","UG":"Uganda","UA":"Ukraine","AE":"United Arab Emirates","GB":"United Kingdom","US":"United States","UY":"Uruguay","UZ":"Uzbekistan","VU":"Vanuatu","VA":"Vatican City","VE":"Venezuela","VN":"Vietnam","WF":"Wallis & Futuna","EH":"Western Sahara","YE":"Yemen","ZM":"Zambia","ZW":"Zimbabwe"}');
        let countryElement = document.getElementById('country');

        for (const c in countries) {
            if (countries.hasOwnProperty(c)) {
                countryElement.add(new Option(countries[c], c));
            }
        }

        form.addEventListener('submit', (event) => {
            if (form.checkValidity()) {
                event.preventDefault();

                let key = form.elements['key'].value;
                let username = form.elements['username'].value;
                let password = form.elements['password'].value;
                let email = form.elements['email'].value;
                let firstname = form.elements['firstname'].value;
                let lastname = form.elements['lastname'].value;
                let city = form.elements['city'].value;
                let country = form.elements['country'].value;
                // let campaign = form.elements['campaign'].value;
                let linkUrl = form.elements['link-url'].value;
                let linkAnchor = form.elements['link-anchor'].value;

                let data = {
                    username: username,
                    password: password,
                    email: email,
                    firstname: firstname,
                    lastname: lastname,
                    city: city,
                    country: country
                    // campaign: campaign
                };
                let ts = Math.round((new Date()).getTime() / 1000);
                let wordArray = CryptoJS.enc.Utf8.parse(JSON.stringify(data));
                let dataEncoded = CryptoJS.enc.Base64.stringify(wordArray);
                let signature = CryptoJS.HmacSHA256(dataEncoded + ts, key).toString();
                let searchParams = new URLSearchParams({
                    subscription_data: dataEncoded,
                    subscription_ts: ts,
                    subscription_signature: signature
                });
                let url = `${linkUrl}?${searchParams.toString()}`;

                document.getElementById('result-url').value = url;
                document.getElementById('result-link').value = `<a href="${url}">${linkAnchor}</a>`;

                result.style.display = 'block';
            } else {
                result.style.display = 'none';
            }
        }, false);
    });

    function copyToClipboard(selector) {
        let copyText = document.querySelector(selector);

        copyText.select();
        copyText.setSelectionRange(0, 99999);

        document.execCommand('copy');
    }
</script>
</body>
</html>

```

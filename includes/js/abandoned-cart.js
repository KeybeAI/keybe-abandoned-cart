fetch( '/wp-json/keybe-data/v1/company-data' )
.then( response => response.json() )
.then( data => {
  const keybeCompanyId = data.keybe_company_id;
  const keybeAppId = data.keybe_app_id;
  const keybeApiKey = data.keybe_api_key;
  const keybeCountryCode = data.keybe_country_code;
  console.log('KeyBe Abandoned Cart Ready!! 🚀');
  jQuery('form[name="checkout"]').change((item) => {
    if (["billing_phone", "billing_email"].includes(item.target.name)) {
      var values = jQuery('form[name="checkout"]').serializeArray();
      console.log(values);
      fetch('https://wrzy3jtldi.execute-api.us-east-1.amazonaws.com/prod/woocommerce/generate-abandoned-cart', {
        method: 'POST',
        headers: new Headers({
          'Content-Type': 'application/json'
        }),
        body: JSON.stringify({
          userData: {
            name: values.find((item) => item.name === 'billing_first_name')?.value,
            lastName: values.find((item) => item.name === 'billing_last_name')?.value,
            phone: keybeCountryCode + values.find((item) => item.name === 'billing_phone')?.value,
            email: values.find((item) => item.name === 'billing_email')?.value,
          },
          keybeClientData: {
            companyUUID: keybeCompanyId,
            appUUID: keybeAppId,
            publicKey: keybeApiKey,
            haveSearchPhoneUser: true,
          },
        }),
      });
    }
  })
});
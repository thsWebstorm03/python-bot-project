tailwind.config = {
    theme: {
        container: {
            // you can configure the container to be centered
            center: true,

            // or have default horizontal padding
            padding: '1rem',

            // default breakpoints but with 40px removed
            screens: {
                sm: '600px',
                md: '728px',
                lg: '984px',
                xl: '1240px',
                '2xl': '1440px',
            },
        },
        extend: {
            colors: {
                primary: {
                    normal: '#6941C6',
                    light: '#7F56D9',
                    lighter: '#8A62E1',
                    lightest: '#E9D7FE'
                },
            }
            
        }
    }
}
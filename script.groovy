def buildApp(){
  echo 'Building the application...'
  echo "Building version ${params.VERSION}"
}

def testApp(){
  echo 'Testing the application...'
}

def deployApp(){
  echo 'Deploy the application...'
  echo "Deploy version ${params.VERSION}"
}

return this
